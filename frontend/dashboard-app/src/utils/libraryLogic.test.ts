import test from 'node:test';
import assert from 'node:assert/strict';
import {
  calculateReadingProgress,
  formatMinutesToHoursAndMinutes,
  calculateOverdueFine,
  canBorrowBook,
  extendLoanDueDate,
  filterOpacBooks,
  toggleBookmarkId,
  addToToteBag,
  maskMemberIdentity,
  type Member,
  type BookItem,
  type CirculationRecord,
} from './libraryLogic';

const createMockBook = (overrides: Partial<BookItem> = {}): BookItem => ({
  id: 'b1',
  title: 'Harry Potter and the Deathly Hallows',
  titleHighlight: 'Harry Potter',
  titleItalic: 'and the Deathly Hallows',
  author: 'J.K. Rowling',
  publisher: 'Bloomsbury Publishing & Gramedia Pustaka Utama',
  year: 2007,
  isbn: '978-0-545-01022-1',
  ddcCode: '823.914 ROW h',
  shelfLocation: 'Lantai 2 · Rak Fiksi A-04',
  category: 'Fiksi & Sastra',
  formats: ['Fisik', 'E-Book'],
  borrowedCount: 1420,
  totalPages: 759,
  availableCopies: 3,
  totalCopies: 6,
  rating: 4.9,
  readersCount: 3376487,
  avgReadTimeMinutes: 448,
  synopsis: 'Novel fantasi penutup seri Harry Potter.',
  branches: [],
  ebookChapters: [],
  coverGradient: 'from-amber-500 via-orange-600 to-red-900',
  coverAccent: '#F59E0B',
  coverPattern: 'constellation',
  ...overrides,
});

const mockMember: Member = {
  id: 'm1',
  memberCode: 'BIB-2026-0842',
  name: 'Amy Winnar',
  role: 'Anggota Utama',
  email: 'amy.winnar@biblioversity.ac.id',
  phone: '0812-8849-2011',
  department: 'Sastra & Kajian Budaya',
  joinDate: '2024-09-01',
  expiryDate: '2027-09-01',
  status: 'Aktif',
  maxQuota: 4,
  currentBorrowed: 1,
  readingLevel: 2,
  readerPoints: 88242,
  nextLevelPoints: 88387,
  dayStreak: 7,
  readMinutesTotal: 384,
  rankGlobal: 2339,
  percentile: 'Top 1%',
  avatarBg: 'from-amber-400 to-rose-500',
  avatarInitials: 'AW',
  badges: [],
};

test('calculateReadingProgress calculates percentage and clamps boundaries', () => {
  const res = calculateReadingProgress(354, 759);
  assert.equal(res.percentage, 47);
  assert.equal(res.clampedPage, 354);
  assert.equal(res.pagesRemaining, 405);

  const over = calculateReadingProgress(900, 759);
  assert.equal(over.percentage, 100);
  assert.equal(over.clampedPage, 759);
  assert.equal(over.pagesRemaining, 0);
});

test('formatMinutesToHoursAndMinutes formats reading time like 6:24', () => {
  assert.equal(formatMinutesToHoursAndMinutes(384), '6:24');
  assert.equal(formatMinutesToHoursAndMinutes(65), '1:05');
  assert.equal(formatMinutesToHoursAndMinutes(0), '0:00');
});

test('calculateOverdueFine computes days overdue and IDR fine accurately', () => {
  const overdue = calculateOverdueFine('2026-11-01', '2026-11-05', 2500);
  assert.equal(overdue.daysOverdue, 4);
  assert.equal(overdue.fineAmount, 10000);

  const onTime = calculateOverdueFine('2026-11-10', '2026-11-05', 2500);
  assert.equal(onTime.daysOverdue, 0);
  assert.equal(onTime.fineAmount, 0);
});

test('canBorrowBook validates member status, stock, quota, and duplicate loans', () => {
  const mockBook = createMockBook();
  assert.deepEqual(canBorrowBook(mockMember, mockBook, []), { allowed: true });

  const noStock = createMockBook({ availableCopies: 0 });
  assert.equal(canBorrowBook(mockMember, noStock, []).allowed, false);

  const duplicateLoan: CirculationRecord = {
    id: 'l1',
    loanCode: 'SRK-01',
    bookId: 'b1',
    bookTitle: mockBook.title,
    bookAuthor: mockBook.author,
    ddcCode: mockBook.ddcCode,
    shelfLocation: mockBook.shelfLocation,
    memberId: 'm1',
    memberName: mockMember.name,
    memberCode: mockMember.memberCode,
    borrowDate: '2026-11-01',
    dueDate: '2026-11-15',
    returnDate: null,
    currentPage: 354,
    totalPages: 759,
    readTimeMinutes: 384,
    status: 'Dipinjam',
    renewalCount: 0,
    maxRenewals: 2,
    fineAmount: 0,
  };
  assert.equal(canBorrowBook(mockMember, mockBook, [duplicateLoan]).allowed, false);
});

test('extendLoanDueDate adds 7 days and increments renewalCount', () => {
  const loan: CirculationRecord = {
    id: 'l1',
    loanCode: 'SRK-01',
    bookId: 'b1',
    bookTitle: 'The Alchemist',
    bookAuthor: 'Paulo Coelho',
    ddcCode: '869.3 COE a',
    shelfLocation: 'Lantai 2 · Rak A-08',
    memberId: 'm1',
    memberName: 'Amy Winnar',
    memberCode: 'BIB-2026-0842',
    borrowDate: '2026-11-01',
    dueDate: '2026-11-10',
    returnDate: null,
    currentPage: 100,
    totalPages: 208,
    readTimeMinutes: 120,
    status: 'Dipinjam',
    renewalCount: 0,
    maxRenewals: 2,
    fineAmount: 0,
  };

  const extended = extendLoanDueDate(loan);
  assert.equal(extended.ok, true);
  assert.equal(extended.updatedLoan?.dueDate, '2026-11-17');
  assert.equal(extended.updatedLoan?.renewalCount, 1);
  assert.equal(extended.updatedLoan?.status, 'Diperpanjang');
});

test('filterOpacBooks filters by query, category, and availability', () => {
  const books: BookItem[] = [
    createMockBook(),
    createMockBook({
      id: 'b2',
      title: 'Designing Data-Intensive Applications',
      titleHighlight: 'Designing Data-Intensive',
      titleItalic: 'Applications',
      author: 'Martin Kleppmann',
      isbn: '978-1-449-37332-0',
      ddcCode: '005.74 KLE d',
      shelfLocation: 'Lantai 3 · Rak C-12',
      category: 'Sains & Teknologi',
      availableCopies: 0,
    }),
  ];

  assert.equal(filterOpacBooks(books, '005.74', 'Semua', false).length, 1);
  assert.equal(filterOpacBooks(books, '', 'Sains & Teknologi', true).length, 0);
  assert.equal(filterOpacBooks(books, 'Rowling', 'Fiksi & Sastra', true).length, 1);
});

test('toggleBookmarkId and addToToteBag manage favorites and loan tote bag accurately', () => {
  const b1 = toggleBookmarkId([], 'b1');
  assert.equal(b1.added, true);
  assert.deepEqual(b1.nextIds, ['b1']);

  const b2 = toggleBookmarkId(b1.nextIds, 'b1');
  assert.equal(b2.added, false);
  assert.deepEqual(b2.nextIds, []);

  const mockBook = createMockBook();
  const tote1 = addToToteBag([], mockBook, mockMember, []);
  assert.equal(tote1.ok, true);
  assert.deepEqual(tote1.nextToteIds, ['b1']);

  const toteDuplicate = addToToteBag(tote1.nextToteIds, mockBook, mockMember, []);
  assert.equal(toteDuplicate.ok, false);
});

test('maskMemberIdentity masks after 4 characters unless revealed', () => {
  assert.equal(maskMemberIdentity('3173042608990001', false), '3173************');
  assert.equal(maskMemberIdentity('3173042608990001', true), '3173042608990001');
});

