import React, { useState, useEffect } from 'react';
import {
  LayoutGrid,
  User,
  BookOpen,
  Repeat,
  CreditCard,
  Search,
  X,
  Trash2,
  RefreshCw,
  AlertCircle,
} from 'lucide-react';
import {
  type Member,
  type BookItem,
  type CirculationRecord,
  type TransactionRecord,
  toggleBookmarkId,
  addToToteBag,
} from './utils/libraryLogic';
import {
  INITIAL_MEMBERS,
  INITIAL_BOOKS,
  INITIAL_CIRCULATION,
  INITIAL_TRANSACTIONS,
} from './data/seedData';
import { api } from './services/portalApi';
import {
  mapProfileToMember,
  mapCatalogToBookItem,
  mapLoansToCirculation,
  mapFinesToTransactions,
} from './utils/dataMappers';
import { OverviewTab } from './components/OverviewTab';
import { ProfilePage } from './components/ProfilePage';
import { DigitalMemberCardModal } from './components/DigitalMemberCardModal';
import { OpacTab } from './components/OpacTab';
import { CirculationTab } from './components/CirculationTab';
import { TransactionsTab } from './components/TransactionsTab';
import { BookDetailPage } from './components/BookDetailPage';
import { RightSideBar } from './components/RightSideBar';

type ActiveMenu = 'overview' | 'members' | 'opac' | 'circulation' | 'transactions' | 'book-detail';
const REFERENCE_DATE = new Date().toISOString().split('T')[0];

export default function App() {
  const [activeMenu, setActiveMenu] = useState<ActiveMenu>('overview');
  const [previousMenu, setPreviousMenu] = useState<ActiveMenu>('overview');
  const [members, setMembers] = useState<Member[]>(INITIAL_MEMBERS);
  const [activeMemberId, setActiveMemberId] = useState<string>(INITIAL_MEMBERS[0].id);
  const [books, setBooks] = useState<BookItem[]>(INITIAL_BOOKS);
  const [selectedBookId, setSelectedBookId] = useState<string>(INITIAL_BOOKS[0].id);
  const [circulation, setCirculation] = useState<CirculationRecord[]>(INITIAL_CIRCULATION);
  const [transactions, setTransactions] = useState<TransactionRecord[]>(INITIAL_TRANSACTIONS);
  const [bookmarkedIds, setBookmarkedIds] = useState<string[]>([]);
  const [toteBagIds, setToteBagIds] = useState<string[]>([]);
  const [isToteBagOpen, setIsToteBagOpen] = useState<boolean>(false);
  const [isDigitalCardOpen, setIsDigitalCardOpen] = useState<boolean>(false);
  const [isSupportModalOpen, setIsSupportModalOpen] = useState<boolean>(false);
  const [globalSearch, setGlobalSearch] = useState<string>('');
  const [isLoading, setIsLoading] = useState<boolean>(true);
  const [apiError, setApiError] = useState<string | null>(null);

  // Sinkronisasi data dari Database Backend INLISLite
  const fetchPortalData = async () => {
    setIsLoading(true);
    setApiError(null);
    try {
      // 1. Profil & Metrik Anggota
      const profile = await api.getProfile();
      const mappedMember = mapProfileToMember(profile);
      setMembers([mappedMember]);
      setActiveMemberId(mappedMember.id);

      // 2. Koleksi Katalog Buku
      const catalogs = await api.getCatalogs('', 30);
      if (catalogs.length > 0) {
        const mappedBooks = catalogs.map(mapCatalogToBookItem);
        setBooks(mappedBooks);
        setSelectedBookId(mappedBooks[0].id);
      }

      // 3. Peminjaman Aktif Anggota
      const activeLoans = await api.getLoans(profile.memberNo);
      const mappedCirculation = mapLoansToCirculation(activeLoans, mappedMember);
      setCirculation(mappedCirculation);

      // 4. Riwayat Transaksi & Pelanggaran / Denda
      const history = await api.getHistory(profile.memberNo);
      const mappedTrx = mapFinesToTransactions(history.fines, mappedMember);
      setTransactions(mappedTrx);
    } catch (err: any) {
      console.warn('Gagal memuat API portal riil, menggunakan fallback mock:', err);
      setApiError('Menggunakan data cache lokal. Backend API dapat diakses saat server aktif.');
    } finally {
      setIsLoading(false);
    }
  };

  useEffect(() => {
    fetchPortalData();
  }, []);

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
    const targetBook = books.find((b) => b.id === bookId);
    if (!targetBook) return;
    const result = addToToteBag(toteBagIds, targetBook, activeMember, circulation);
    if (result.ok) setToteBagIds(result.nextToteIds);
  };

  const handleCheckoutToteBag = () => {
    const newLoans: CirculationRecord[] = toteBagIds.map((bId) => {
      const b = books.find((bk) => bk.id === bId)!;
      return {
        id: `loan-${Date.now()}-${bId}`,
        loanCode: `SRK-${Math.floor(1000 + Math.random() * 9000)}`,
        bookId: b.id,
        bookTitle: b.title,
        bookAuthor: b.author,
        ddcCode: b.ddcCode,
        shelfLocation: b.shelfLocation,
        memberId: activeMember.id,
        memberName: activeMember.name,
        memberCode: activeMember.memberCode,
        borrowDate: REFERENCE_DATE,
        dueDate: new Date(Date.now() + 7 * 86400000).toISOString().split('T')[0],
        returnDate: null,
        currentPage: 1,
        totalPages: b.totalPages,
        readTimeMinutes: 0,
        status: 'Dipinjam',
        renewalCount: 0,
        maxRenewals: 2,
        fineAmount: 0,
      };
    });
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
      dueDate: new Date(Date.now() + 7 * 86400000).toISOString().split('T')[0],
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
    setSelectedBookId(targetBook.id);
  };

  const handleReturnBook = (loanId: string) => {
    setCirculation((prev) =>
      prev.map((c) => (c.id === loanId ? { ...c, status: 'Dikembalikan', returnDate: REFERENCE_DATE } : c))
    );
  };

  const handleExtendLoan = async (loanId: string) => {
    try {
      await api.extendLoan(loanId);
      // Refresh pinjaman aktif dari server
      const loans = await api.getLoans(activeMember.memberCode);
      setCirculation(mapLoansToCirculation(loans, activeMember));
    } catch {
      // Fallback update in-memory
      setCirculation((prev) =>
        prev.map((c) => {
          if (c.id === loanId) {
            const nextDue = new Date(new Date(c.dueDate).getTime() + 7 * 86400000).toISOString().split('T')[0];
            return { ...c, dueDate: nextDue, renewalCount: c.renewalCount + 1, status: 'Diperpanjang' };
          }
          return c;
        })
      );
    }
  };

  const handleUpdateReadingProgress = (loanId: string, pages: number, mins: number) => {
    setCirculation((prev) =>
      prev.map((c) =>
        c.id === loanId
          ? {
              ...c,
              currentPage: Math.min(c.totalPages, c.currentPage + pages),
              readTimeMinutes: c.readTimeMinutes + mins,
            }
          : c
      )
    );
  };

  const handleCreateTransaction = (newTrx: Omit<TransactionRecord, 'id' | 'invoiceCode' | 'date'>) => {
    setTransactions((prev) => [
      { ...newTrx, id: `trx-${Date.now()}`, invoiceCode: `TRX-2026-${transactions.length}`, date: REFERENCE_DATE },
      ...prev,
    ]);
  };

  const handleAddBook = (newBookData: Omit<BookItem, 'id'>) => {
    const created: BookItem = { ...newBookData, id: `book-${Date.now()}` };
    setBooks((prev) => [created, ...prev]);
    setSelectedBookId(created.id);
  };

  return (
    <div className="min-h-screen bg-slate-50 text-slate-900">
      <div className="max-w-[1536px] mx-auto flex min-h-screen">
        {/* Sidebar Navigasi Khusus Anggota */}
        <aside className="w-64 border-r border-slate-200 bg-white p-6 shrink-0 flex flex-col justify-between">
          <div>
            <div className="flex items-center gap-2 mb-8">
              <div className="w-9 h-9 rounded-xl bg-sky-600 flex items-center justify-center text-white font-black text-lg shadow-md shadow-sky-200">
                IN
              </div>
              <div>
                <h1 className="font-bold text-base leading-none text-slate-800">Perpus Portal</h1>
                <p className="text-xs text-slate-400 mt-0.5">Layanan Mandiri Anggota</p>
              </div>
            </div>

            <nav className="space-y-1.5">
              {[
                { id: 'overview', label: 'Beranda Pemustaka', icon: LayoutGrid },
                { id: 'opac', label: 'Katalog Koleksi', icon: BookOpen },
                { id: 'circulation', label: 'Pinjaman Saya', icon: Repeat },
                { id: 'transactions', label: 'Riwayat & Denda', icon: CreditCard },
                { id: 'members', label: 'Profil Saya', icon: User },
              ].map((item) => {
                const Icon = item.icon;
                const isActive = activeMenu === item.id;
                return (
                  <button
                    key={item.id}
                    onClick={() => setActiveMenu(item.id as ActiveMenu)}
                    className={`w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all cursor-pointer ${
                      isActive
                        ? 'bg-sky-600 text-white shadow-sm shadow-sky-300'
                        : 'text-slate-600 hover:bg-slate-100/80 hover:text-slate-900'
                    }`}
                  >
                    <Icon className="w-4 h-4" />
                    {item.label}
                  </button>
                );
              })}
            </nav>
          </div>

          <div className="pt-6 border-t border-slate-100">
            <button
              onClick={() => setIsDigitalCardOpen(true)}
              className="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-sky-50 border border-sky-200 text-sky-700 rounded-xl text-xs font-bold hover:bg-sky-100 transition-colors"
            >
              Kartu Anggota Digital
            </button>
          </div>
        </aside>

        {/* Konten Utama */}
        <main className="flex-1 p-8 min-w-0">
          <div className="flex items-center justify-between mb-8 gap-4">
            <div className="relative flex-1 max-w-md">
              <Search className="absolute left-3.5 top-2.5 w-4 h-4 text-slate-400" />
              <input
                type="text"
                value={globalSearch}
                onChange={(e) => setGlobalSearch(e.target.value)}
                placeholder="Cari judul koleksi, pengarang, subjek..."
                className="w-full pl-10 pr-4 py-2 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 shadow-xs"
              />
            </div>

            <div className="flex items-center gap-3">
              {isLoading && (
                <div className="flex items-center gap-2 text-xs text-sky-600 bg-sky-50 px-3 py-1.5 rounded-lg border border-sky-100">
                  <RefreshCw className="w-3.5 h-3.5 animate-spin" />
                  <span>Sinkron database...</span>
                </div>
              )}
              {apiError && (
                <div className="flex items-center gap-1.5 text-xs text-amber-600 bg-amber-50 px-3 py-1.5 rounded-lg border border-amber-200">
                  <AlertCircle className="w-3.5 h-3.5" />
                  <span>Mode Offline</span>
                </div>
              )}
            </div>
          </div>

          {activeMenu === 'overview' && (
            <OverviewTab
              activeMember={activeMember}
              books={books}
              circulation={circulation}
              selectedBookId={selectedBookId}
              bookmarkedIds={bookmarkedIds}
              toteBagIds={toteBagIds}
              onOpenBookDetail={handleOpenBookDetail}
              onToggleBookmark={handleToggleBookmark}
              onAddToToteBag={handleAddToToteBag}
              onNavigateTab={(tab) => setActiveMenu(tab as ActiveMenu)}
              onOpenToteBagDrawer={() => setIsToteBagOpen(true)}
              referenceDate={REFERENCE_DATE}
            />
          )}

          {activeMenu === 'opac' && (
            <OpacTab
              books={books}
              activeMember={activeMember}
              circulation={circulation}
              selectedBookId={selectedBookId}
              bookmarkedIds={bookmarkedIds}
              toteBagIds={toteBagIds}
              onSelectBook={setSelectedBookId}
              onOpenBookDetail={handleOpenBookDetail}
              onBorrowBook={handleBorrowBook}
              onToggleBookmark={handleToggleBookmark}
              onAddToToteBag={handleAddToToteBag}
              onAddBook={handleAddBook}
              globalSearchQuery={globalSearch}
            />
          )}

          {activeMenu === 'book-detail' && (
            <BookDetailPage
              book={selectedBook}
              allBooks={books}
              activeMember={activeMember}
              circulation={circulation}
              isBookmarked={bookmarkedIds.includes(selectedBook.id)}
              isInToteBag={toteBagIds.includes(selectedBook.id)}
              onBack={() => setActiveMenu(previousMenu)}
              onSelectBook={setSelectedBookId}
              onToggleBookmark={handleToggleBookmark}
              onAddToToteBag={handleAddToToteBag}
              onOpenToteBagDrawer={() => setIsToteBagOpen(true)}
              onRecordEbookReading={(bId, p) =>
                handleUpdateReadingProgress(circulation.find((c) => c.bookId === bId)?.id || '', p, 15)
              }
            />
          )}

          {activeMenu === 'members' && (
            <ProfilePage
              activeMember={activeMember}
              books={books}
              circulation={circulation}
              transactions={transactions}
              bookmarkedIds={bookmarkedIds}
              referenceDate={REFERENCE_DATE}
              onOpenDigitalCard={() => setIsDigitalCardOpen(true)}
              onOpenBookDetail={handleOpenBookDetail}
              onNavigateTab={(tab) => setActiveMenu(tab as ActiveMenu)}
              onOpenSupportModal={() => setIsSupportModalOpen(true)}
            />
          )}

          {activeMenu === 'circulation' && (
            <CirculationTab
              circulation={circulation}
              activeMember={activeMember}
              referenceDate={REFERENCE_DATE}
              onExtendLoan={handleExtendLoan}
              onReturnBook={handleReturnBook}
              onUpdateReadingProgress={handleUpdateReadingProgress}
            />
          )}

          {activeMenu === 'transactions' && (
            <TransactionsTab
              transactions={transactions}
              activeMember={activeMember}
              onCreateTransaction={handleCreateTransaction}
            />
          )}
        </main>

        {/* Panel Samping Ringkasan */}
        <div className="w-80 border-l border-slate-200 bg-slate-50 p-6 shrink-0 sticky top-0 h-screen overflow-y-auto">
          <RightSideBar activeMenu={activeMenu} selectedBook={selectedBook} />
        </div>

        {/* Modal Kartu Anggota Digital */}
        <DigitalMemberCardModal
          isOpen={isDigitalCardOpen}
          member={activeMember}
          onClose={() => setIsDigitalCardOpen(false)}
        />

        {/* Drawer Tote Bag Keranjang Peminjaman */}
        {isToteBagOpen && (
          <div className="fixed inset-0 z-50 bg-black/50 backdrop-blur-xs flex items-center justify-center p-4">
            <div className="bg-white rounded-3xl p-6 w-full max-w-lg shadow-2xl">
              <div className="flex items-center justify-between mb-4">
                <h2 className="text-xl font-bold text-slate-800">Tas Koleksi Saya</h2>
                <button
                  onClick={() => setIsToteBagOpen(false)}
                  className="p-1 rounded-lg hover:bg-slate-100 text-slate-500"
                >
                  <X className="w-5 h-5" />
                </button>
              </div>
              <div className="space-y-3 mb-6 max-h-60 overflow-y-auto">
                {books
                  .filter((b) => toteBagIds.includes(b.id))
                  .map((b) => (
                    <div
                      key={b.id}
                      className="flex items-center justify-between p-3 border border-slate-100 bg-slate-50 rounded-xl"
                    >
                      <span className="font-semibold text-sm text-slate-800 line-clamp-1">{b.title}</span>
                      <button
                        onClick={() => setToteBagIds(toteBagIds.filter((id) => id !== b.id))}
                        className="text-rose-500 hover:text-rose-700 p-1"
                      >
                        <Trash2 className="w-4 h-4" />
                      </button>
                    </div>
                  ))}
                {toteBagIds.length === 0 && (
                  <p className="text-sm text-slate-400 text-center py-6">Belum ada buku di dalam tas.</p>
                )}
              </div>
              <button
                onClick={handleCheckoutToteBag}
                disabled={toteBagIds.length === 0}
                className="w-full py-3 bg-sky-600 disabled:bg-slate-200 text-white font-bold rounded-xl hover:bg-sky-700 transition-colors"
              >
                Konfirmasi Peminjaman
              </button>
            </div>
          </div>
        )}
      </div>
    </div>
  );
}
