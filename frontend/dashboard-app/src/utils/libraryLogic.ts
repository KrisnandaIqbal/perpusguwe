export interface BadgeItem {
  id: string;
  name: string;
  description: string;
  iconName: 'lightbulb' | 'shield-check' | 'compass' | 'hourglass' | 'book-open' | 'award';
  unlocked: boolean;
}

export interface Member {
  id: string;
  memberCode: string;
  name: string;
  role: 'Anggota Utama' | 'Peneliti' | 'Mahasiswa' | 'Umum';
  email: string;
  phone: string;
  department: string;
  joinDate: string;
  expiryDate: string;
  status: 'Aktif' | 'Bebas Pustaka' | 'Tertahan Denda';
  maxQuota: number;
  currentBorrowed: number;
  readingLevel: number;
  readerPoints: number;
  nextLevelPoints: number;
  dayStreak: number;
  readMinutesTotal: number;
  rankGlobal: number;
  percentile: string;
  avatarBg: string;
  avatarInitials: string;
  badges: BadgeItem[];
  nikId?: string;
  nppNumber?: string;
  address?: string;
  joinDateFormatted?: string;
  expiryDateFormatted?: string;
}

export interface BranchAvailability {
  branchId: string;
  branchName: string;
  cityArea: string;
  floorAndShelf: string;
  callNumber: string;
  availableCount: number;
  totalCount: number;
  status: 'Tersedia' | 'Dipinjam Semua' | 'Reservasi Rak';
}

export interface EbookChapter {
  chapterNumber: number;
  title: string;
  pagesRange: string;
  content: string[];
}

export interface BookItem {
  id: string;
  title: string;
  titleHighlight: string;
  titleItalic: string;
  author: string;
  publisher: string;
  year: number;
  isbn: string;
  ddcCode: string;
  shelfLocation: string;
  category:
    | 'Fiksi & Sastra'
    | 'Sains & Teknologi'
    | 'Filsafat & Psikologi'
    | 'Sejarah & Budaya'
    | 'Seni & Desain'
    | 'Ekonomi & Bisnis';
  formats: ('Fisik' | 'E-Book')[];
  borrowedCount: number;
  totalPages: number;
  availableCopies: number;
  totalCopies: number;
  rating: number;
  readersCount: number;
  avgReadTimeMinutes: number;
  synopsis: string;
  extendedSynopsis?: string;
  branches: BranchAvailability[];
  ebookChapters: EbookChapter[];
  coverGradient: string;
  coverAccent: string;
  coverPattern: 'constellation' | 'geometric' | 'editorial' | 'botanical' | 'archival' | 'cyber';
  isTrending?: boolean;
  isLiveShowcase?: boolean;
  audioDuration?: string;
}

export interface CirculationRecord {
  id: string;
  loanCode: string;
  bookId: string;
  bookTitle: string;
  bookAuthor: string;
  ddcCode: string;
  shelfLocation: string;
  memberId: string;
  memberName: string;
  memberCode: string;
  borrowDate: string;
  dueDate: string;
  returnDate: string | null;
  currentPage: number;
  totalPages: number;
  readTimeMinutes: number;
  status: 'Dipinjam' | 'Terlambat' | 'Dikembalikan' | 'Diperpanjang';
  renewalCount: number;
  maxRenewals: number;
  fineAmount: number;
}

export interface TransactionRecord {
  id: string;
  invoiceCode: string;
  memberId: string;
  memberName: string;
  memberCode: string;
  type:
    | 'Denda Keterlambatan'
    | 'Perpanjangan Keanggotaan'
    | 'Deposit Koleksi Khusus'
    | 'Bebas Pustaka & Legalisir';
  relatedLoanCode?: string;
  description: string;
  date: string;
  amount: number;
  paymentMethod: 'QRIS Perpustakaan' | 'Saldo BiblioPay' | 'Transfer VA' | 'Tunai Loket';
  status: 'Lunas' | 'Menunggu Pembayaran' | 'Dibatalkan';
}

export interface ActivityComment {
  id: string;
  userName: string;
  userRole: string;
  avatarBg: string;
  initials: string;
  bookTitle: string;
  chapterLabel: string;
  comment: string;
  timestamp: string;
  likes: number;
  isLiked?: boolean;
  replies: {
    id: string;
    userName: string;
    initials: string;
    avatarBg: string;
    comment: string;
    timestamp: string;
  }[];
}

export interface FriendProgress {
  id: string;
  name: string;
  initials: string;
  avatarBg: string;
  page: number;
  points: number;
  rank: number;
  streak: number;
  statusText: string;
}

export const DAILY_FINE_RATE_IDR = 2500;

export function calculateReadingProgress(currentPage: number, totalPages: number): {
  percentage: number;
  clampedPage: number;
  pagesRemaining: number;
} {
  if (totalPages <= 0) {
    return { percentage: 0, clampedPage: 0, pagesRemaining: 0 };
  }
  const clampedPage = Math.max(0, Math.min(currentPage, totalPages));
  const percentage = Math.round((clampedPage / totalPages) * 100);
  const pagesRemaining = Math.max(0, totalPages - clampedPage);
  return { percentage, clampedPage, pagesRemaining };
}

export function formatMinutesToHoursAndMinutes(totalMinutes: number): string {
  const safeMinutes = Math.max(0, Math.floor(totalMinutes));
  const hours = Math.floor(safeMinutes / 60);
  const mins = safeMinutes % 60;
  return `${hours}:${mins.toString().padStart(2, '0')}`;
}

export function calculateOverdueFine(
  dueDateIso: string,
  referenceDateIso: string,
  dailyRate: number = DAILY_FINE_RATE_IDR
): { daysOverdue: number; fineAmount: number } {
  const due = new Date(dueDateIso);
  const ref = new Date(referenceDateIso);
  due.setUTCHours(0, 0, 0, 0);
  ref.setUTCHours(0, 0, 0, 0);
  const diffMs = ref.getTime() - due.getTime();
  const daysOverdue = Math.max(0, Math.floor(diffMs / (1000 * 60 * 60 * 24)));
  return {
    daysOverdue,
    fineAmount: daysOverdue * dailyRate,
  };
}

export function calculateDaysRemaining(dueDateIso: string, referenceDateIso: string): number {
  const due = new Date(dueDateIso);
  const ref = new Date(referenceDateIso);
  due.setUTCHours(0, 0, 0, 0);
  ref.setUTCHours(0, 0, 0, 0);
  const diffMs = due.getTime() - ref.getTime();
  return Math.round(diffMs / (1000 * 60 * 60 * 24));
}

export function canBorrowBook(
  member: Member,
  book: BookItem,
  activeLoans: CirculationRecord[]
): { allowed: boolean; reason?: string } {
  if (member.status === 'Tertahan Denda') {
    return {
      allowed: false,
      reason: 'Keanggotaan sedang tertahan karena denda belum lunas.',
    };
  }
  if (book.availableCopies <= 0) {
    return {
      allowed: false,
      reason: 'Stok eksemplar buku ini sedang habis dipinjam.',
    };
  }
  const memberActiveLoans = activeLoans.filter(
    (l) => l.memberId === member.id && l.status !== 'Dikembalikan'
  );
  if (memberActiveLoans.length >= member.maxQuota) {
    return {
      allowed: false,
      reason: `Kuota pinjaman penuh (${member.maxQuota} buku maksimal).`,
    };
  }
  const alreadyBorrowed = memberActiveLoans.some((l) => l.bookId === book.id);
  if (alreadyBorrowed) {
    return {
      allowed: false,
      reason: 'Anggota sudah meminjam judul buku yang sama.',
    };
  }
  return { allowed: true };
}

export function extendLoanDueDate(
  loan: CirculationRecord
): { ok: boolean; updatedLoan?: CirculationRecord; reason?: string } {
  if (loan.status === 'Dikembalikan') {
    return { ok: false, reason: 'Buku yang sudah dikembalikan tidak dapat diperpanjang.' };
  }
  if (loan.status === 'Terlambat') {
    return {
      ok: false,
      reason: 'Pinjaman yang melewati tenggat tidak dapat diperpanjang otomatis.',
    };
  }
  if (loan.renewalCount >= loan.maxRenewals) {
    return {
      ok: false,
      reason: `Batas maksimal perpanjangan (${loan.maxRenewals}x) telah tercapai.`,
    };
  }

  const currentDue = new Date(loan.dueDate);
  currentDue.setUTCDate(currentDue.getUTCDate() + 7);
  const nextDueIso = currentDue.toISOString().split('T')[0];

  return {
    ok: true,
    updatedLoan: {
      ...loan,
      dueDate: nextDueIso,
      renewalCount: loan.renewalCount + 1,
      status: 'Diperpanjang',
    },
  };
}

export function filterOpacBooks(
  books: BookItem[],
  query: string,
  category: string,
  onlyAvailable: boolean
): BookItem[] {
  const normalizedQuery = query.trim().toLowerCase();
  return books.filter((book) => {
    if (category !== 'Semua' && book.category !== category) {
      return false;
    }
    if (onlyAvailable && book.availableCopies <= 0) {
      return false;
    }
    if (!normalizedQuery) {
      return true;
    }
    return (
      book.title.toLowerCase().includes(normalizedQuery) ||
      book.author.toLowerCase().includes(normalizedQuery) ||
      book.isbn.toLowerCase().includes(normalizedQuery) ||
      book.ddcCode.toLowerCase().includes(normalizedQuery) ||
      book.shelfLocation.toLowerCase().includes(normalizedQuery)
    );
  });
}

export function formatIdr(amount: number): string {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    maximumFractionDigits: 0,
  }).format(amount);
}

export function toggleBookmarkId(savedIds: string[], bookId: string): {
  nextIds: string[];
  added: boolean;
} {
  if (savedIds.includes(bookId)) {
    return {
      nextIds: savedIds.filter((id) => id !== bookId),
      added: false,
    };
  }
  return {
    nextIds: [...savedIds, bookId],
    added: true,
  };
}

export function addToToteBag(
  currentToteIds: string[],
  book: BookItem,
  member: Member,
  activeLoans: CirculationRecord[]
): { ok: boolean; nextToteIds: string[]; reason?: string } {
  if (currentToteIds.includes(book.id)) {
    return {
      ok: false,
      nextToteIds: currentToteIds,
      reason: 'Buku ini sudah ada di dalam Tote Bag peminjaman Anda.',
    };
  }
  const eligibility = canBorrowBook(member, book, activeLoans);
  if (!eligibility.allowed) {
    return {
      ok: false,
      nextToteIds: currentToteIds,
      reason: eligibility.reason,
    };
  }
  const memberActiveLoans = activeLoans.filter(
    (l) => l.memberId === member.id && l.status !== 'Dikembalikan'
  );
  if (memberActiveLoans.length + currentToteIds.length >= member.maxQuota) {
    return {
      ok: false,
      nextToteIds: currentToteIds,
      reason: `Total pinjaman aktif + Tote Bag melebihi kuota (${member.maxQuota} buku).`,
    };
  }
  return {
    ok: true,
    nextToteIds: [...currentToteIds, book.id],
  };
}

export function maskMemberIdentity(rawIdentity: string, revealed: boolean): string {
  const cleaned = rawIdentity.trim();
  if (revealed || cleaned.length <= 4) {
    return cleaned;
  }
  const prefix = cleaned.slice(0, 4);
  const stars = '*'.repeat(Math.max(8, cleaned.length - 4));
  return `${prefix}${stars}`;
}
