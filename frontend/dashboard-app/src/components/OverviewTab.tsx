import React, { useState } from 'react';
import {
  BookOpen,
  Share2,
  ChevronRight,
  CheckCircle2,
  Search,
  Users,
  Repeat,
  CreditCard,
  ShoppingBag,
  Bookmark,
  Star,
} from 'lucide-react';
import {
  type Member,
  type BookItem,
  type CirculationRecord,
  calculateReadingProgress,
  calculateDaysRemaining,
} from '../utils/libraryLogic';
import { BookCoverArt } from './BookCoverArt';
import { RekomendasiCard, type BukuRekomendasi } from './RekomendasiCard';

interface OverviewTabProps {
  activeMember: Member;
  books: BookItem[];
  circulation: CirculationRecord[];
  selectedBookId: string;
  bookmarkedIds: string[];
  toteBagIds: string[];
  onOpenBookDetail: (bookId: string) => void;
  onToggleBookmark?: (bookId: string) => void;
  onAddToToteBag?: (bookId: string) => void;
  onNavigateTab: (tab: 'overview' | 'members' | 'opac' | 'circulation' | 'transactions') => void;
  onOpenToteBagDrawer: () => void;
  referenceDate: string;
}

interface BarisRekomendasi {
  judul: string;
  buku: BukuRekomendasi[];
}

declare global {
  interface Window {
    __REKOMENDASI__?: { profilUrl: string; baris: BarisRekomendasi[] };
  }
}

export const OverviewTab: React.FC<OverviewTabProps> = ({
  activeMember,
  books,
  circulation,
  selectedBookId,
  bookmarkedIds,
  toteBagIds,
  onOpenBookDetail,
  onToggleBookmark,
  onAddToToteBag,
  onNavigateTab,
  onOpenToteBagDrawer,
  referenceDate,
}) => {
  const rekomendasi = window.__REKOMENDASI__?.baris ?? [];
  const rekomendasiProfilUrl = window.__REKOMENDASI__?.profilUrl ?? '#';
  const [homeSearchQuery, setHomeSearchQuery] = useState('');
  const [selectedCategory, setSelectedCategory] = useState<'Semua' | BookItem['category']>('Semua');
  const [onlySavedFilter, setOnlySavedFilter] = useState(false);
  const [shareFeedback, setShareFeedback] = useState(false);

  const memberActiveLoans = circulation.filter(
    (c) => c.memberId === activeMember.id && c.status !== 'Dikembalikan'
  );
  const primaryLoan =
    memberActiveLoans.find((c) => c.bookId === selectedBookId) ||
    memberActiveLoans[0] ||
    circulation[0];

  const featuredBook =
    books.find((b) => b.id === (primaryLoan?.bookId || selectedBookId)) || books[0];

  const homeFilteredBooks = books.filter((b) => {
    if (onlySavedFilter && !bookmarkedIds.includes(b.id)) return false;
    if (selectedCategory !== 'Semua' && b.category !== selectedCategory) return false;
    if (!homeSearchQuery.trim()) return true;
    const q = homeSearchQuery.toLowerCase();
    return (
      b.title.toLowerCase().includes(q) ||
      b.author.toLowerCase().includes(q) ||
      b.publisher.toLowerCase().includes(q) ||
      b.category.toLowerCase().includes(q) ||
      b.ddcCode.toLowerCase().includes(q)
    );
  });

  const handleShareBook = () => {
    setShareFeedback(true);
    setTimeout(() => setShareFeedback(false), 2500);
  };

  const categories: ('Semua' | BookItem['category'])[] = [
    'Semua',
    'Fiksi & Sastra',
    'Sains & Teknologi',
    'Filsafat & Psikologi',
    'Sejarah & Budaya',
    'Seni & Desain',
  ];

  return (
    <div className="space-y-7">
      {/* 1. PENCARIAN BERANDA & GRID MENU LAYANAN CEPAT */}
      <section aria-label="Pencarian Beranda dan Grid Menu Layanan" className="space-y-4">
        {/* Search Bar on Beranda */}
        <div className="bg-white border border-slate-200 rounded-3xl p-4 flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
          <div className="relative flex-1">
            <Search className="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2 z-10 pointer-events-none" />
            <input
              type="text"
              value={homeSearchQuery}
              onChange={(e) => setHomeSearchQuery(e.target.value)}
              placeholder="Cari koleksi buku di Beranda..."
              aria-label="Cari buku di halaman Beranda"
              className="w-full pl-10 pr-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm text-slate-900 placeholder:text-slate-700 focus:outline-none focus:ring-2 focus:ring-sky-500"
            />
          </div>

          <button
            type="button"
            onClick={() => {
              const el = document.getElementById('beranda-daftar-buku');
              el?.scrollIntoView({ behavior: 'smooth' });
            }}
            className="px-5 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-700 text-white text-sm font-bold shadow-sm transition-colors cursor-pointer whitespace-nowrap"
          >
            Lihat Daftar Buku ({homeFilteredBooks.length})
          </button>
        </div>

        {/* Grid Menu Layanan Perpustakaan */}
        <div
          aria-label="Grid Menu Layanan"
          className="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3"
        >
          {[
            { id: 'opac', icon: BookOpen, label: 'Rak Buku', sub: `${books.length} Koleksi` },
            { id: 'members', icon: Users, label: 'Anggota', sub: 'Profil & Kartu' },
            { id: 'circulation', icon: Repeat, label: 'Sirkulasi', sub: `${memberActiveLoans.length} Aktif` },
            { id: 'transactions', icon: CreditCard, label: 'Transaksi', sub: 'Riwayat' },
            { id: 'toteBag', icon: ShoppingBag, label: 'Tote Bag', sub: `${toteBagIds.length} Buku`, onClick: onOpenToteBagDrawer },
            { id: 'favorites', icon: Bookmark, label: 'Favorit', sub: `${bookmarkedIds.length} Tersimpan`, onClick: () => { setOnlySavedFilter(!onlySavedFilter); const el = document.getElementById('beranda-daftar-buku'); el?.scrollIntoView({ behavior: 'smooth' }); } },
          ].map((item, idx) => (
            <button
              key={idx}
              type="button"
              onClick={item.onClick || (() => onNavigateTab(item.id as any))}
              className="p-4 rounded-2xl bg-white border border-slate-200 hover:border-sky-300 flex flex-col items-start gap-2 cursor-pointer text-left transition-all"
            >
              <div className="w-9 h-9 rounded-lg bg-sky-50 text-sky-700 flex items-center justify-center">
                <item.icon className="w-5 h-5" />
              </div>
              <div>
                <p className="text-xs font-bold text-slate-800">{item.label}</p>
                <p className="text-[11px] text-slate-700 font-mono-tabular">{item.sub}</p>
              </div>
            </button>
          ))}
        </div>
      </section>

      {/* 2. BANNER KOLEKSI UTAMA */}
      <section
        aria-label="Banner Koleksi Utama"
        className="relative overflow-hidden rounded-3xl p-6 bg-slate-900 text-white"
      >
        <div className="relative z-10 flex flex-col md:flex-row items-start md:items-center gap-6">
          <div
            onClick={() => onOpenBookDetail(featuredBook.id)}
            role="button"
            tabIndex={0}
            className="cursor-pointer transition-transform hover:scale-105 shrink-0"
          >
            <BookCoverArt book={featuredBook} size="md" className="w-28 h-40 md:w-32 md:h-44" />
          </div>

          <div className="flex-1 min-w-0">
            <div className="flex flex-wrap items-center gap-2 mb-2">
              <button
                type="button"
                onClick={handleShareBook}
                className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-700 text-xs font-medium text-white transition-colors cursor-pointer"
              >
                {shareFeedback ? <CheckCircle2 className="w-3.5 h-3.5 text-emerald-400" /> : <Share2 className="w-3.5 h-3.5" />}
                <span>{shareFeedback ? 'Tersalin' : 'Bagikan'}</span>
              </button>
              <span className="px-3 py-1 rounded-full bg-sky-900 text-xs text-sky-200 font-mono-tabular ml-auto">
                Format: {featuredBook.formats.join(' + ')}
              </span>
            </div>

            <h1 className="text-2xl sm:text-3xl font-bold tracking-tight leading-tight">
              {featuredBook.titleHighlight} <span className="italic font-normal text-sky-300">{featuredBook.titleItalic}</span>
            </h1>

            <p className="mt-2 text-sm text-slate-300 line-clamp-2 max-w-2xl leading-relaxed">
              {featuredBook.synopsis}
            </p>

            <div className="mt-4">
              <button
                type="button"
                onClick={() => onOpenBookDetail(featuredBook.id)}
                className="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-sky-600 hover:bg-sky-700 text-white text-xs font-bold transition-transform active:scale-95 cursor-pointer"
              >
                Detail & Baca E-Book
              </button>
            </div>
          </div>
        </div>
      </section>

      {/* Rekomendasi asli dari server (window.__REKOMENDASI__, diisi PHP di Dashboard\Viewsnggota) */}
      <section id="beranda-daftar-buku" aria-labelledby="rek-judul" className="space-y-6">
        <div>
          <h2 id="rek-judul" className="text-lg font-extrabold text-slate-900">
            Disarankan untukmu
          </h2>
          <p className="text-xs text-slate-700">
            Dari riwayat pinjam dan koleksi unit kerjamu. Atur di{' '}
            <a href={rekomendasiProfilUrl} className="font-bold text-sky-700 hover:underline">
              Profil
            </a>
            .
          </p>
        </div>
        {rekomendasi.length === 0 && (
          <p className="text-sm text-slate-700">Belum ada saran buku untukmu.</p>
        )}
        {rekomendasi.map((baris) => (
          <div key={baris.judul} className="space-y-3">
            <h3 className="text-sm font-bold text-slate-900">{baris.judul}</h3>
            <div className="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
              {baris.buku.map((b) => (
                <RekomendasiCard key={b.id} buku={b} />
              ))}
            </div>
          </div>
        ))}
      </section>
    </div>
  );
};
