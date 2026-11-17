import React, { useState, useEffect } from 'react';
import {
  LayoutGrid,
  Users,
  BookOpen,
  Repeat,
  CreditCard,
  Search,
  X,
  ShoppingBag,
  Trash2,
  User,
  QrCode,
} from 'lucide-react';
import {
  type Member,
  type BookItem,
  type CirculationRecord,
  type TransactionRecord,
  canBorrowBook,
  extendLoanDueDate,
  toggleBookmarkId,
  addToToteBag,
} from './utils/libraryLogic';
import {
  INITIAL_MEMBERS,
  INITIAL_BOOKS,
  INITIAL_CIRCULATION,
  INITIAL_TRANSACTIONS,
} from './data/seedData';
import { OverviewTab } from './components/OverviewTab';
import { ProfilePage } from './components/ProfilePage';
import { DigitalMemberCardModal } from './components/DigitalMemberCardModal';
import { OpacTab } from './components/OpacTab';
import { CirculationTab } from './components/CirculationTab';
import { TransactionsTab } from './components/TransactionsTab';
import { BookDetailPage } from './components/BookDetailPage';
import { RightSideBar } from './components/RightSideBar';
import { BookCoverArt } from './components/BookCoverArt';

type ActiveMenu = 'overview' | 'members' | 'opac' | 'circulation' | 'transactions' | 'book-detail';
const REFERENCE_DATE = '2026-11-09';

export default function App() {
  const [activeMenu, setActiveMenu] = useState<ActiveMenu>('overview');
  const [previousMenu, setPreviousMenu] = useState<ActiveMenu>('overview');
  const [members, setMembers] = useState<Member[]>(INITIAL_MEMBERS);
  const [activeMemberId, setActiveMemberId] = useState<string>(INITIAL_MEMBERS[0].id);
  const [books, setBooks] = useState<BookItem[]>(INITIAL_BOOKS);
  const [selectedBookId, setSelectedBookId] = useState<string>(INITIAL_BOOKS[0].id);
  const [circulation, setCirculation] = useState<CirculationRecord[]>(INITIAL_CIRCULATION);
  const [transactions, setTransactions] = useState<TransactionRecord[]>(INITIAL_TRANSACTIONS);
  const [bookmarkedIds, setBookmarkedIds] = useState<string[]>(['book-hp7', 'book-bumi']);
  const [toteBagIds, setToteBagIds] = useState<string[]>(['book-1984']);
  const [isToteBagOpen, setIsToteBagOpen] = useState<boolean>(false);
  const [isDigitalCardOpen, setIsDigitalCardOpen] = useState<boolean>(false);
  const [isSupportModalOpen, setIsSupportModalOpen] = useState<boolean>(false);
  const [globalSearch, setGlobalSearch] = useState<string>('');

  const activeMember = members.find((m) => m.id === activeMemberId) || members[0];
  const selectedBook = books.find((b) => b.id === selectedBookId) || books[0];

  const handleOpenBookDetail = (bookId: string) => {
    if (activeMenu !== 'book-detail') setPreviousMenu(activeMenu);
    setSelectedBookId(bookId);
    setActiveMenu('book-detail');
    window.scrollTo({ top: 0, behavior: 'smooth' });
  };

  const handleToggleBookmark = (bookId: string) => {
    setBookmarkedIds(toggleBookmarkId(bookmarkedIds, bookId).nextIds);
  };

  const handleAddToToteBag = (bookId: string) => {
    const result = addToToteBag(toteBagIds, books.find((b) => b.id === bookId)!, activeMember, circulation);
    if (result.ok) setToteBagIds(result.nextToteIds);
  };

  const handleCheckoutToteBag = () => {
    const newLoans: CirculationRecord[] = toteBagIds.map((bId, index) => ({
      id: `circ-${Date.now()}-${index}`,
      loanCode: `SRK-2611-${String(circulation.length + index + 20).padStart(3, '0')}`,
      bookId: bId,
      bookTitle: books.find((b) => b.id === bId)?.title || '',
      bookAuthor: books.find((b) => b.id === bId)?.author || '',
      ddcCode: books.find((b) => b.id === bId)?.ddcCode || '',
      shelfLocation: books.find((b) => b.id === bId)?.shelfLocation || '',
      memberId: activeMember.id,
      memberName: activeMember.name,
      memberCode: activeMember.memberCode,
      borrowDate: REFERENCE_DATE,
      dueDate: '2026-11-23',
      returnDate: null,
      currentPage: 1,
      totalPages: books.find((b) => b.id === bId)?.totalPages || 100,
      readTimeMinutes: 5,
      status: 'Dipinjam',
      renewalCount: 0,
      maxRenewals: 2,
      fineAmount: 0,
    }));
    setCirculation((prev) => [...newLoans, ...prev]);
    setToteBagIds([]);
    setIsToteBagOpen(false);
  };

  const handleBorrowBook = (bookId: string) => {
    const targetBook = books.find((b) => b.id === bookId);
    if (!targetBook) return;
    const newLoan: CirculationRecord = {
      id: `circ-${Date.now()}`,
      loanCode: `SRK-2611-${String(circulation.length + 15).padStart(3, '0')}`,
      bookId: targetBook.id,
      bookTitle: targetBook.title,
      bookAuthor: targetBook.author,
      ddcCode: targetBook.ddcCode,
      shelfLocation: targetBook.shelfLocation,
      memberId: activeMember.id,
      memberName: activeMember.name,
      memberCode: activeMember.memberCode,
      borrowDate: REFERENCE_DATE,
      dueDate: '2026-11-23',
      returnDate: null,
      currentPage: 1,
      totalPages: targetBook.totalPages,
      readTimeMinutes: 5,
      status: 'Dipinjam',
      renewalCount: 0,
      maxRenewals: 2,
      fineAmount: 0,
    };
    setCirculation((prev) => [newLoan, ...prev]);
    setBooks((prev) => prev.map((b) => b.id === targetBook.id ? { ...b, availableCopies: Math.max(0, b.availableCopies - 1), borrowedCount: b.borrowedCount + 1 } : b));
    setSelectedBookId(targetBook.id);
  };

  const handleReturnBook = (loanId: string) => {
    setCirculation((prev) => prev.map((c) => c.id === loanId ? { ...c, status: 'Dikembalikan', returnDate: REFERENCE_DATE } : c));
  };

  const handleExtendLoan = (loanId: string) => {
    setCirculation((prev) => prev.map((c) => c.id === loanId ? extendLoanDueDate(c).updatedLoan! : c));
  };

  const handleUpdateReadingProgress = (loanId: string, pages: number, mins: number) => {
    setCirculation((prev) => prev.map((c) => c.id === loanId ? { ...c, currentPage: Math.min(c.totalPages, c.currentPage + pages), readTimeMinutes: c.readTimeMinutes + mins } : c));
  };

  const handleCreateTransaction = (newTrx: Omit<TransactionRecord, 'id' | 'invoiceCode' | 'date'>) => {
    setTransactions((prev) => [{ ...newTrx, id: `trx-${Date.now()}`, invoiceCode: `TRX-2026-${transactions.length}`, date: REFERENCE_DATE }, ...prev]);
  };

  const handleAddBook = (newBookData: Omit<BookItem, 'id'>) => {
    const created: BookItem = { ...newBookData, id: `book-${Date.now()}` };
    setBooks((prev) => [created, ...prev]);
    setSelectedBookId(created.id);
  };

  return (
    <div className="min-h-screen bg-slate-50 text-slate-900">
      <div className="max-w-[1536px] mx-auto flex min-h-screen">
        <aside className="w-64 border-r border-slate-200 bg-white p-6 shrink-0">
          <div className="font-bold text-xl text-slate-800 mb-8">BiblioVerse</div>
          <nav className="space-y-2">
            {[
              { id: 'overview', label: 'Beranda', icon: LayoutGrid },
              { id: 'members', label: 'Data Anggota', icon: Users },
              { id: 'opac', label: 'Rak Buku', icon: BookOpen },
              { id: 'circulation', label: 'Sirkulasi', icon: Repeat },
              { id: 'transactions', label: 'Transaksi', icon: CreditCard },
            ].map((item) => {
              const Icon = item.icon;
              const isActive = activeMenu === item.id;
              return (
                <button
                  key={item.id}
                  onClick={() => setActiveMenu(item.id as ActiveMenu)}
                  className={`w-full flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-semibold cursor-pointer ${
                    isActive ? 'bg-sky-50 text-sky-700' : 'text-slate-600 hover:bg-slate-50'
                  }`}
                >
                  <Icon className="w-5 h-5" />
                  {item.label}
                </button>
              );
            })}
          </nav>
        </aside>

        <main className="flex-1 p-8 min-w-0">
          <div className="flex items-center justify-between mb-8">
            <div className="relative w-96">
              <Search className="absolute left-3 top-2.5 w-4 h-4 text-slate-400" />
              <input type="text" placeholder="Cari buku, anggota..." className="w-full pl-10 pr-4 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500" />
            </div>
          </div>
          {activeMenu === 'overview' && <OverviewTab activeMember={activeMember} books={books} circulation={circulation} selectedBookId={selectedBookId} bookmarkedIds={bookmarkedIds} toteBagIds={toteBagIds} onOpenBookDetail={handleOpenBookDetail} onToggleBookmark={handleToggleBookmark} onAddToToteBag={handleAddToToteBag} onNavigateTab={(tab) => setActiveMenu(tab as ActiveMenu)} onOpenToteBagDrawer={() => setIsToteBagOpen(true)} referenceDate={REFERENCE_DATE} />}
          {activeMenu === 'opac' && <OpacTab books={books} activeMember={activeMember} circulation={circulation} selectedBookId={selectedBookId} bookmarkedIds={bookmarkedIds} toteBagIds={toteBagIds} onSelectBook={setSelectedBookId} onOpenBookDetail={handleOpenBookDetail} onBorrowBook={handleBorrowBook} onToggleBookmark={handleToggleBookmark} onAddToToteBag={handleAddToToteBag} onAddBook={handleAddBook} globalSearchQuery={globalSearch} />}
          {activeMenu === 'book-detail' && <BookDetailPage book={selectedBook} allBooks={books} activeMember={activeMember} circulation={circulation} isBookmarked={bookmarkedIds.includes(selectedBook.id)} isInToteBag={toteBagIds.includes(selectedBook.id)} onBack={() => setActiveMenu(previousMenu)} onSelectBook={setSelectedBookId} onToggleBookmark={handleToggleBookmark} onAddToToteBag={handleAddToToteBag} onOpenToteBagDrawer={() => setIsToteBagOpen(true)} onRecordEbookReading={(bId, p) => handleUpdateReadingProgress(circulation.find(c => c.bookId === bId)?.id || '', p, 15)} />}
          {activeMenu === 'members' && <ProfilePage activeMember={activeMember} books={books} circulation={circulation} transactions={transactions} bookmarkedIds={bookmarkedIds} referenceDate={REFERENCE_DATE} onOpenDigitalCard={() => setIsDigitalCardOpen(true)} onOpenBookDetail={handleOpenBookDetail} onNavigateTab={(tab) => setActiveMenu(tab as ActiveMenu)} onOpenSupportModal={() => setIsSupportModalOpen(true)} />}
          {activeMenu === 'circulation' && <CirculationTab circulation={circulation} activeMember={activeMember} referenceDate={REFERENCE_DATE} onExtendLoan={handleExtendLoan} onReturnBook={handleReturnBook} onUpdateReadingProgress={handleUpdateReadingProgress} />}
          {activeMenu === 'transactions' && <TransactionsTab transactions={transactions} activeMember={activeMember} onCreateTransaction={handleCreateTransaction} />}
        </main>

        <div className="w-80 border-l border-slate-200 bg-slate-50 p-6 shrink-0 sticky top-0 h-screen overflow-y-auto">
          <RightSideBar activeMenu={activeMenu} selectedBook={selectedBook} />
        </div>

        <DigitalMemberCardModal isOpen={isDigitalCardOpen} member={activeMember} onClose={() => setIsDigitalCardOpen(false)} />
        
        {isToteBagOpen && (
          <div className="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4">
             <div className="bg-white rounded-3xl p-6 w-full max-w-lg shadow-2xl">
               <div className="flex items-center justify-between mb-4">
                 <h2 className="text-xl font-bold">Tote Bag Peminjaman</h2>
                 <button onClick={() => setIsToteBagOpen(false)}><X className="w-6 h-6"/></button>
               </div>
               <div className="space-y-4 mb-6">
                 {books.filter(b => toteBagIds.includes(b.id)).map(b => (
                   <div key={b.id} className="flex items-center justify-between p-3 border rounded-xl">
                     <span className="font-semibold text-sm">{b.title}</span>
                     <button onClick={() => setToteBagIds(toteBagIds.filter(id => id !== b.id))} className="text-rose-500"><Trash2 className="w-4 h-4"/></button>
                   </div>
                 ))}
               </div>
               <button onClick={handleCheckoutToteBag} className="w-full py-3 bg-sky-600 text-white font-bold rounded-xl">Konfirmasi Pinjam</button>
             </div>
          </div>
        )}
      </div>
    </div>
  );
}
