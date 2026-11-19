import React, { useState } from 'react';
import {
  ArrowLeft,
  Bookmark,
  BookOpen,
  ShoppingBag,
  Star,
  Users,
  Layers,
  MapPin,
  CheckCircle2,
  Building2,
  Sparkles,
  Plus,
} from 'lucide-react';
import { type BookItem, type Member, type CirculationRecord } from '../utils/libraryLogic';
import { BookCoverArt } from './BookCoverArt';
import { BookGridCard } from './BookGridCard';
import { EbookReaderModal } from './EbookReaderModal';

interface BookDetailPageProps {
  book: BookItem;
  allBooks: BookItem[];
  activeMember: Member;
  circulation: CirculationRecord[];
  isBookmarked: boolean;
  isInToteBag: boolean;
  onBack: () => void;
  onSelectBook: (bookId: string) => void;
  onToggleBookmark: (bookId: string) => void;
  onAddToToteBag: (bookId: string) => void;
  onOpenToteBagDrawer: () => void;
  onRecordEbookReading: (bookId: string, pagesRead: number) => void;
}

export const BookDetailPage: React.FC<BookDetailPageProps> = ({
  book,
  allBooks,
  isBookmarked,
  isInToteBag,
  onBack,
  onSelectBook,
  onToggleBookmark,
  onAddToToteBag,
  onOpenToteBagDrawer,
  onRecordEbookReading,
}) => {
  const [isReaderOpen, setIsReaderOpen] = useState(false);

  const relatedBooks = allBooks
    .filter((b) => b.id !== book.id && (b.category === book.category || b.isTrending))
    .slice(0, 4);

  return (
    <div className="space-y-7" data-testid="book-detail-full-page">
      {/* Top Breadcrumb & Back Navigation */}
      <div className="flex flex-wrap items-center justify-between gap-4">
        <button
          type="button"
          onClick={onBack}
          className="glacier-pill inline-flex items-center gap-2 px-4 py-2 rounded-2xl text-xs sm:text-sm font-bold text-slate-800 dark:text-white hover:bg-sky-500/20 transition-colors cursor-pointer"
        >
          <ArrowLeft className="w-4 h-4" />
          <span>Kembali ke Beranda & Katalog</span>
        </button>

        <div className="flex items-center gap-2 text-xs text-slate-700 dark:text-sky-200/80 font-mono-tabular">
          <span>Katalog OPAC</span>
          <span aria-hidden="true">/</span>
          <span>{book.category}</span>
          <span aria-hidden="true">/</span>
          <span className="text-slate-900 dark:text-white font-semibold">{book.ddcCode}</span>
        </div>
      </div>

      {/* Main Hero & Bibliographic Detail Section (Transparent Glacier Glass) */}
      <section
        aria-label="Informasi Utama Koleksi Buku"
        className="glacier-card relative overflow-hidden rounded-3xl p-6 sm:p-8 text-slate-900 dark:text-white"
      >
        {/* Decorative Glacier Frost Glow Orbs */}
        <div
          aria-hidden="true"
          className="pointer-events-none absolute -top-20 right-10 w-72 h-72 rounded-full bg-sky-400/25 dark:bg-sky-400/20 blur-3xl"
        />
        <div
          aria-hidden="true"
          className="pointer-events-none absolute -bottom-20 left-12 w-64 h-64 rounded-full bg-cyan-400/20 dark:bg-indigo-500/20 blur-3xl"
        />

        <div className="relative z-10 flex flex-col lg:flex-row items-start gap-7">
          {/* Book Cover Column */}
          <div className="flex flex-col items-center gap-3 shrink-0 mx-auto lg:mx-0">
            <BookCoverArt
              book={book}
              size="lg"
              className="w-44 h-64 sm:w-48 sm:h-68 shadow-2xl"
            />
            <span className="glacier-pill px-3 py-1 rounded-full text-[11px] font-mono-tabular text-slate-700 dark:text-sky-200">
              ISBN {book.isbn}
            </span>
          </div>

          {/* Right Metadata, Synopsis & Interactive Action Buttons */}
          <div className="flex-1 min-w-0 space-y-4">
            {/* Format Tag: Fisik + E-Book & Category */}
            <div className="flex flex-wrap items-center gap-2.5 text-xs">
              <span
                data-testid="detail-format-tag"
                className="glacier-pill px-3 py-1 rounded-xl text-sky-800 dark:text-amber-300 font-bold tracking-wide"
              >
                Format: {book.formats.join(' + ')}
              </span>
              <span className="text-slate-400 dark:text-slate-300">·</span>
              <span className="text-sky-700 dark:text-sky-200 font-semibold">{book.category}</span>
              <span className="text-slate-400 dark:text-slate-300">·</span>
              <span className="font-mono-tabular text-slate-700 dark:text-sky-100/80">
                DDC {book.ddcCode}
              </span>
            </div>

            {/* Book Title */}
            <h1 className="text-2xl sm:text-4xl font-extrabold tracking-tight text-slate-900 dark:text-white leading-tight text-balance">
              <span>{book.titleHighlight} </span>
              <span className="font-serif-editorial italic font-normal text-sky-700 dark:text-amber-200">
                {book.titleItalic}
              </span>
            </h1>

            {/* Author & Publisher Grid */}
            <div className="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-1 pb-3 border-b border-sky-200/50 dark:border-sky-300/15 text-xs">
              <div className="glacier-tile rounded-2xl p-3">
                <p className="text-slate-700 dark:text-sky-200/75">Penulis Utama</p>
                <p className="font-bold text-slate-900 dark:text-white text-sm mt-0.5">
                  {book.author}
                </p>
              </div>
              <div className="glacier-tile rounded-2xl p-3">
                <p className="text-slate-700 dark:text-sky-200/75">Penerbit Resmi</p>
                <p className="font-bold text-slate-900 dark:text-white text-sm mt-0.5 flex items-center gap-1.5">
                  <Building2 className="w-3.5 h-3.5 text-sky-600 dark:text-sky-300 shrink-0" />
                  <span className="truncate">{book.publisher}</span>
                </p>
              </div>
              <div className="glacier-tile rounded-2xl p-3">
                <p className="text-slate-700 dark:text-sky-200/75">Tahun Terbit & Ketebalan</p>
                <p className="font-bold font-mono-tabular text-slate-900 dark:text-white text-sm mt-0.5">
                  {book.year} · {book.totalPages} Halaman
                </p>
              </div>
            </div>

            {/* Interactive Action Buttons: Simpan, Baca E-Book, + Pinjam (Tote Bag) */}
            <div className="flex flex-wrap items-center gap-3 pt-1">
              {/* 1. Tombol + Pinjam (Tote Bag) */}
              <button
                type="button"
                onClick={() => {
                  if (isInToteBag) {
                    onOpenToteBagDrawer();
                  } else {
                    onAddToToteBag(book.id);
                  }
                }}
                data-testid="detail-add-totebag-btn"
                className={`inline-flex items-center gap-2 px-5 py-3 rounded-2xl text-xs sm:text-sm font-extrabold shadow-lg transition-all cursor-pointer whitespace-nowrap ${
                  isInToteBag
                    ? 'bg-emerald-600 hover:bg-emerald-500 text-white'
                    : 'bg-[#FF6B35] hover:bg-[#e85a25] text-white'
                }`}
              >
                {isInToteBag ? (
                  <>
                    <ShoppingBag className="w-4 h-4" />
                    <span>Di Tote Bag (Lihat Tote Bag)</span>
                  </>
                ) : (
                  <>
                    <Plus className="w-4 h-4 stroke-[2.5]" />
                    <span>+ Pinjam (Tote Bag)</span>
                  </>
                )}
              </button>

              {/* 2. Tombol Baca E-Book (membuka in-app reader modal) */}
              <button
                type="button"
                onClick={() => setIsReaderOpen(true)}
                data-testid="detail-read-ebook-btn"
                className="inline-flex items-center gap-2 px-5 py-3 rounded-2xl bg-gradient-to-r from-sky-500 to-cyan-500 hover:from-sky-400 hover:to-cyan-400 text-white text-xs sm:text-sm font-extrabold shadow-lg border border-white/30 transition-all cursor-pointer whitespace-nowrap"
              >
                <BookOpen className="w-4 h-4" />
                <span>Baca E-Book</span>
              </button>

              {/* 3. Tombol Simpan (Bookmark ke Favorit) */}
              <button
                type="button"
                onClick={() => onToggleBookmark(book.id)}
                data-testid="detail-bookmark-btn"
                className={`inline-flex items-center gap-2 px-4 py-3 rounded-2xl text-xs sm:text-sm font-bold transition-colors cursor-pointer whitespace-nowrap ${
                  isBookmarked
                    ? 'bg-amber-400 text-slate-950 border border-amber-300 shadow-md'
                    : 'glacier-pill text-slate-800 dark:text-white hover:bg-sky-500/20'
                }`}
              >
                <Bookmark className={`w-4 h-4 ${isBookmarked ? 'fill-current' : ''}`} />
                <span>{isBookmarked ? 'Tersimpan di Favorit' : 'Simpan'}</span>
              </button>
            </div>

            {/* Important Statistics Bento Row (Transparent Glacier Glass Tiles): Social Proof, Star Rating, Stock Count */}
            <div className="grid grid-cols-1 sm:grid-cols-3 gap-3.5 pt-3">
              {/* Social Proof */}
              <div className="glacier-tile relative overflow-hidden rounded-2xl p-4 flex flex-col justify-between">
                <div
                  aria-hidden="true"
                  className="pointer-events-none absolute -top-8 -right-8 w-24 h-24 rounded-full bg-amber-400/25 blur-2xl"
                />
                <div className="flex items-center justify-between text-[11px] font-extrabold text-amber-700 dark:text-amber-300">
                  <span>SOCIAL PROOF SIRKULASI</span>
                  <Users className="w-4 h-4" />
                </div>
                <p
                  data-testid="detail-borrowed-count"
                  className="text-xl sm:text-2xl font-extrabold font-mono-tabular text-slate-900 dark:text-white mt-1.5"
                >
                  Telah Dipinjam {book.borrowedCount.toLocaleString('id-ID')} Kali
                </p>
                <p className="text-[11px] font-semibold text-slate-700 dark:text-sky-100/80 mt-0.5">
                  Koleksi populer anggota perpustakaan
                </p>
              </div>

              {/* Star Rating */}
              <div className="glacier-tile relative overflow-hidden rounded-2xl p-4 flex flex-col justify-between">
                <div
                  aria-hidden="true"
                  className="pointer-events-none absolute -top-8 -right-8 w-24 h-24 rounded-full bg-orange-400/25 blur-2xl"
                />
                <div className="flex items-center justify-between text-[11px] font-extrabold text-orange-700 dark:text-orange-300">
                  <span>RATING PEMBACA</span>
                  <Star className="w-4 h-4 fill-current" />
                </div>
                <div className="flex items-baseline gap-2 mt-1.5">
                  <span className="text-2xl sm:text-3xl font-extrabold font-mono-tabular text-slate-900 dark:text-white">
                    {book.rating.toFixed(1)}
                  </span>
                  <span className="text-xs font-bold text-slate-700 dark:text-sky-200">
                    / 5.0 Bintang
                  </span>
                </div>
                <p className="text-[11px] font-semibold text-slate-700 dark:text-sky-100/80 mt-0.5">
                  Dari {book.readersCount.toLocaleString('id-ID')} ulasan anggota
                </p>
              </div>

              {/* Stock Count */}
              <div className="glacier-tile relative overflow-hidden rounded-2xl p-4 flex flex-col justify-between">
                <div
                  aria-hidden="true"
                  className="pointer-events-none absolute -top-8 -right-8 w-24 h-24 rounded-full bg-emerald-400/25 blur-2xl"
                />
                <div className="flex items-center justify-between text-[11px] font-extrabold text-emerald-700 dark:text-emerald-300">
                  <span>JUMLAH STOK FISIK</span>
                  <Layers className="w-4 h-4" />
                </div>
                <p className="text-xl sm:text-2xl font-extrabold font-mono-tabular text-slate-900 dark:text-white mt-1.5">
                  {book.availableCopies} / {book.totalCopies} Eksemplar
                </p>
                <p className="text-[11px] font-semibold text-slate-700 dark:text-sky-100/80 mt-0.5">
                  {book.availableCopies > 0
                    ? 'Siap dipinjam & tersedia E-Book tanpa batas'
                    : 'Stok fisik dipinjam · E-Book tetap tersedia'}
                </p>
              </div>
            </div>
          </div>
        </div>

        {/* Full Complete Synopsis Section */}
        <div className="relative z-10 mt-7 pt-6 border-t border-sky-200/50 dark:border-sky-300/15 space-y-3">
          <h2 className="text-base font-bold text-sky-700 dark:text-amber-300 flex items-center gap-2">
            <Sparkles className="w-4 h-4" />
            <span>Sinopsis Lengkap & Ringkasan Katalog</span>
          </h2>
          <p className="text-sm text-slate-700 dark:text-slate-200 leading-relaxed">
            {book.synopsis}
          </p>
          {book.extendedSynopsis && (
            <p className="text-sm text-slate-700 dark:text-slate-300 leading-relaxed">
              {book.extendedSynopsis}
            </p>
          )}
        </div>
      </section>

      {/* Tabel Ketersediaan Cabang Fisik Beserta Nomor Rak (Transparent Glacier Glass Card) */}
      <section
        aria-label="Tabel Ketersediaan Cabang Fisik dan Nomor Rak"
        className="glacier-card rounded-3xl p-6 space-y-4"
      >
        <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
          <div>
            <h2 className="text-lg font-bold text-slate-900 dark:text-white">
              Tabel Ketersediaan Cabang Fisik & Nomor Rak
            </h2>
            <p className="text-xs text-slate-700 dark:text-sky-100/80">
              Cek lokasi rak spesifik dan jumlah eksemplar fisik di setiap cabang perpustakaan sebelum berkunjung
            </p>
          </div>
          <span className="glacier-pill px-3 py-1 rounded-full text-xs font-mono-tabular font-semibold text-sky-800 dark:text-sky-200 self-start sm:self-auto">
            Total {book.branches.length} Lokasi Cabang
          </span>
        </div>

        <div className="overflow-x-auto">
          <table className="w-full text-left border-collapse">
            <thead>
              <tr className="border-b border-sky-200/50 dark:border-sky-300/15 text-[11px] font-bold text-slate-700 dark:text-sky-200/80">
                <th className="py-3 px-3">CABANG PERPUSTAKAAN</th>
                <th className="py-3 px-3">LANTAI & NOMOR RAK</th>
                <th className="py-3 px-3">KODE PANGGIL DDC</th>
                <th className="py-3 px-3 text-right">STOK CABANG</th>
                <th className="py-3 px-3">STATUS KETERSEDIAAN</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-sky-200/40 dark:divide-sky-300/10 text-xs">
              {book.branches.map((branch) => (
                <tr
                  key={branch.branchId}
                  className="hover:bg-sky-400/10 dark:hover:bg-sky-400/5 transition-colors"
                >
                  <td className="py-3.5 px-3">
                    <p className="font-bold text-slate-900 dark:text-white">{branch.branchName}</p>
                    <p className="text-[11px] text-slate-700 dark:text-sky-200/75">
                      {branch.cityArea}
                    </p>
                  </td>
                  <td className="py-3.5 px-3 font-semibold text-slate-800 dark:text-slate-200">
                    <span className="inline-flex items-center gap-1.5">
                      <MapPin className="w-3.5 h-3.5 text-sky-600 dark:text-sky-300 shrink-0" />
                      <span>{branch.floorAndShelf}</span>
                    </span>
                  </td>
                  <td className="py-3.5 px-3 font-mono-tabular text-slate-700 dark:text-slate-300">
                    {branch.callNumber}
                  </td>
                  <td className="py-3.5 px-3 text-right font-mono-tabular font-bold text-slate-900 dark:text-white">
                    {branch.availableCount} / {branch.totalCount} eks
                  </td>
                  <td className="py-3.5 px-3 whitespace-nowrap">
                    <span
                      className={`inline-flex items-center gap-1 font-bold ${
                        branch.status === 'Tersedia'
                          ? 'text-emerald-600 dark:text-emerald-400'
                          : branch.status === 'Reservasi Rak'
                          ? 'text-amber-600 dark:text-amber-300'
                          : 'text-rose-600 dark:text-rose-400'
                      }`}
                    >
                      <CheckCircle2 className="w-3.5 h-3.5" />
                      <span>{branch.status}</span>
                    </span>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </section>

      {/* Related Books Section */}
      <section aria-label="Koleksi Serupa di Rak" className="space-y-3">
        <h2 className="text-base font-bold text-slate-900">
          Koleksi Lainnya yang Sering Dipinjam Bersama
        </h2>
        <div className="grid grid-cols-2 sm:grid-cols-4 gap-3.5">
          {relatedBooks.map((rel) => (
            <BookGridCard
              key={rel.id}
              book={rel}
              onClick={onSelectBook}
            />
          ))}
        </div>
      </section>

      {/* In-App E-Book Reader Modal */}
      <EbookReaderModal
        book={book}
        isOpen={isReaderOpen}
        onClose={() => setIsReaderOpen(false)}
        onCompleteReadingSession={(pages) => onRecordEbookReading(book.id, pages)}
      />
    </div>
  );
};
