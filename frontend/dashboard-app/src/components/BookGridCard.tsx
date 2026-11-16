import React from 'react';
import {
  Star,
  BookOpen,
  Bookmark,
  ShoppingBag,
  Sparkles,
  MapPin,
} from 'lucide-react';
import { type BookItem } from '../utils/libraryLogic';
import { BookCoverArt } from './BookCoverArt';

interface BookGridCardProps {
  book: BookItem;
  onClick: (bookId: string) => void;
  isBookmarked?: boolean;
  isInToteBag?: boolean;
  onToggleBookmark?: (bookId: string) => void;
  onAddToToteBag?: (bookId: string) => void;
}

export const BookGridCard: React.FC<BookGridCardProps> = ({
  book,
  onClick,
  isBookmarked = false,
  isInToteBag = false,
  onToggleBookmark,
  onAddToToteBag,
}) => {
  const isAvailable = book.availableCopies > 0;
  const borrowedLabel =
    book.borrowedCount >= 1000
      ? `${(book.borrowedCount / 1000).toFixed(1).replace('.0', '')}rb+ dipinjam`
      : `${book.borrowedCount}+ dipinjam`;

  const shelfShort = book.shelfLocation.split('(')[0].trim();

  return (
    <article
      onClick={() => onClick(book.id)}
      data-testid={`book-grid-card-${book.id}`}
      className="group relative bg-white border border-slate-200/90 hover:border-sky-500/80 rounded-2xl overflow-hidden flex flex-col cursor-pointer transition-all duration-300 hover:-translate-y-1 hover:shadow-[0_14px_30px_-8px_rgba(14,165,233,0.18)]"
    >
      {/* Top Square Showcase Stage */}
      <div className="relative aspect-[4/4.2] bg-gradient-to-b from-slate-50 via-slate-100/75 to-slate-200/60 flex flex-col items-center justify-center p-3 overflow-hidden">
        {/* Soft Ambient Color Halo matching book accent */}
        <div
          aria-hidden="true"
          className="pointer-events-none absolute w-28 h-28 rounded-full blur-2xl opacity-35 group-hover:opacity-60 transition-opacity duration-300"
          style={{ backgroundColor: book.coverAccent || '#38BDF8' }}
        />

        {/* Top-Left Stock Ribbon Badge */}
        <div
          className={`absolute top-2.5 left-0 z-20 pl-2 pr-2.5 py-0.5 rounded-r-full text-[9px] font-extrabold tracking-wide text-white shadow-sm flex items-center gap-1 ${
            isAvailable
              ? 'bg-gradient-to-r from-rose-600 to-orange-500'
              : 'bg-slate-600'
          }`}
        >
          <span className="w-1.5 h-1.5 rounded-full bg-white animate-pulse" />
          <span>{isAvailable ? `Stok ${book.availableCopies}/${book.totalCopies}` : 'Stok Habis'}</span>
        </div>

        {/* Top-Right Quick Bookmark Action or Official Tag */}
        {onToggleBookmark ? (
          <button
            type="button"
            onClick={(e) => {
              e.stopPropagation();
              onToggleBookmark(book.id);
            }}
            aria-label={isBookmarked ? `Hapus ${book.title} dari favorit` : `Simpan ${book.title} ke favorit`}
            title={isBookmarked ? 'Tersimpan di Favorit' : 'Simpan ke Favorit'}
            className={`absolute top-2 right-2 z-20 w-7 h-7 rounded-full flex items-center justify-center transition-all cursor-pointer shadow-xs border ${
              isBookmarked
                ? 'bg-amber-400 border-amber-300 text-slate-950 scale-105'
                : 'bg-white/90 hover:bg-white border-slate-200/90 text-slate-700 hover:text-amber-600'
            }`}
          >
            <Bookmark className={`w-3.5 h-3.5 ${isBookmarked ? 'fill-current' : ''}`} />
          </button>
        ) : (
          <div className="absolute top-2.5 right-2 pointer-events-none z-10">
            <span className="text-[8px] font-extrabold tracking-wider uppercase text-slate-800 bg-white/90 backdrop-blur-xs px-1.5 py-0.5 rounded-md border border-slate-200/90 shadow-2xs">
              OPAC RESMI
            </span>
          </div>
        )}

        {/* Center 3D Book Cover with realistic page thickness & drop shadow */}
        <div className="relative z-10 my-auto pt-2 pb-4 transition-transform duration-300 group-hover:scale-105 group-hover:-rotate-1">
          {/* Simulated white book pages edge on the right */}
          <div
            aria-hidden="true"
            className="absolute inset-y-1 -right-1 w-2 rounded-r-sm bg-gradient-to-r from-slate-200 via-white to-slate-300 border-y border-r border-slate-300/80 shadow-sm"
          />
          {/* Floor shadow under the book */}
          <div
            aria-hidden="true"
            className="absolute -bottom-1.5 inset-x-2 h-2.5 rounded-full bg-slate-900/25 blur-xs"
          />
          <BookCoverArt
            book={book}
            size="md"
            className="w-24 h-33 rounded-lg shadow-[0_8px_20px_rgba(15,23,42,0.3)] ring-1 ring-black/10"
          />
        </div>

        {/* Decorative subtle dot matrix at bottom right */}
        <div
          aria-hidden="true"
          className="absolute bottom-7 right-2 grid grid-cols-4 gap-0.5 opacity-30 pointer-events-none"
        >
          {Array.from({ length: 8 }).map((_, idx) => (
            <span key={idx} className="w-1 h-1 rounded-full bg-slate-700" />
          ))}
        </div>

        {/* Bottom Dark Banner Strip */}
        <div className="absolute bottom-0 inset-x-0 z-20 bg-slate-950/95 text-white h-5.5 flex items-center justify-between text-[8px] font-extrabold tracking-wide uppercase overflow-hidden border-t border-white/10">
          {book.isTrending && (
            <span className="h-full px-1.5 bg-gradient-to-r from-amber-400 to-yellow-300 text-slate-950 flex items-center gap-0.5 font-extrabold shrink-0">
              <Sparkles className="w-2.5 h-2.5 fill-current" />
              <span>POPULER</span>
            </span>
          )}
          <div className="px-2 flex items-center justify-between w-full gap-1 truncate">
            <span className="text-amber-300 truncate">{book.formats.join(' + ')}</span>
            <span className="text-sky-300 font-mono-tabular shrink-0">DDC {book.ddcCode.split(' ')[0]}</span>
          </div>
        </div>
      </div>

      {/* Bottom Card Metadata Body */}
      <div className="p-3 flex-1 flex flex-col justify-between space-y-2 bg-white">
        <div className="space-y-1">
          {/* Category & Year tiny overline */}
          <div className="flex items-center justify-between gap-1 text-[10px] font-semibold text-slate-600">
            <span className="truncate text-sky-700 font-bold">{book.category}</span>
            <span className="font-mono-tabular text-slate-500 shrink-0">{book.year}</span>
          </div>

          {/* Book Title (2 lines max, crisp bold typography) */}
          <h3 className="text-xs font-bold text-slate-900 group-hover:text-sky-700 line-clamp-2 leading-snug min-h-[2.25rem] transition-colors">
            {book.title}
          </h3>
        </div>

        <div className="space-y-1.5 pt-0.5">
          {/* Highlighted Page Count Pill + Author */}
          <div className="flex items-center gap-1.5 flex-wrap">
            <span className="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-rose-50 border border-rose-200/90 text-rose-700 text-[10px] font-extrabold font-mono-tabular">
              <BookOpen className="w-2.5 h-2.5 shrink-0" />
              <span>{book.totalPages} Hal</span>
            </span>
            <span className="text-[10px] font-semibold text-slate-700 truncate flex-1">
              {book.author}
            </span>
          </div>

          {/* Orange Shelf Location Line */}
          <p className="text-[10px] font-bold text-orange-600 flex items-center gap-1 truncate">
            <MapPin className="w-2.5 h-2.5 shrink-0" />
            <span className="truncate">{shelfShort}</span>
          </p>

          {/* Bottom Rating, Social Proof & Quick Borrow Action */}
          <div className="pt-1.5 border-t border-slate-100 flex items-center justify-between gap-1 text-[11px]">
            <div className="flex items-center gap-1 min-w-0 truncate">
              <Star className="w-3.5 h-3.5 fill-amber-400 text-amber-400 shrink-0" />
              <span className="font-extrabold text-slate-900 font-mono-tabular">
                {book.rating.toFixed(1)}
              </span>
              <span className="text-slate-300">·</span>
              <span className="truncate text-[10px] font-medium text-slate-700">
                {borrowedLabel}
              </span>
            </div>

            {onAddToToteBag && (
              <button
                type="button"
                onClick={(e) => {
                  e.stopPropagation();
                  onAddToToteBag(book.id);
                }}
                aria-label={isInToteBag ? `${book.title} sudah di Tote Bag` : `Tambah ${book.title} ke Tote Bag`}
                title={isInToteBag ? 'Sudah di Tote Bag' : 'Masukkan ke Tote Bag Pinjam'}
                className={`px-2 py-0.5 rounded-md text-[10px] font-extrabold flex items-center gap-1 shrink-0 transition-colors cursor-pointer ${
                  isInToteBag
                    ? 'bg-emerald-100 text-emerald-800 border border-emerald-300'
                    : 'bg-sky-50 hover:bg-sky-600 text-sky-700 hover:text-white border border-sky-200'
                }`}
              >
                <ShoppingBag className="w-2.5 h-2.5" />
                <span>{isInToteBag ? 'Di Tas' : '+Pinjam'}</span>
              </button>
            )}
          </div>
        </div>
      </div>
    </article>
  );
};
