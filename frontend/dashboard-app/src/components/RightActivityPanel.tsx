import React from 'react';
import {
  Plus,
  Clock,
  Sparkles,
  BookOpen,
  Cpu,
  Compass,
  Feather,
} from 'lucide-react';
import { type BookItem } from '../utils/libraryLogic';
import { BookCoverArt } from './BookCoverArt';

interface RightActivityPanelProps {
  books: BookItem[];
  selectedBookId: string;
  onSelectBook: (bookId: string) => void;
  onBorrowBook: (bookId: string) => void;
  onNavigateOpacCategory: (category: BookItem['category']) => void;
}

export const RightActivityPanel: React.FC<RightActivityPanelProps> = ({
  books,
  selectedBookId,
  onSelectBook,
  onBorrowBook,
  onNavigateOpacCategory,
}) => {
  const spotlightBook =
    books.find((b) => b.id !== selectedBookId && b.availableCopies > 0) || books[1] || books[0];

  const quickTags: {
    label: string;
    category: BookItem['category'];
    icon: React.FC<{ className?: string }>;
    tint: string;
  }[] = [
    {
      label: 'Fiksi Klasik',
      category: 'Fiksi & Sastra',
      icon: Feather,
      tint: 'text-amber-700 dark:text-amber-300 bg-amber-400/20 border-amber-400/40 dark:border-amber-300/30',
    },
    {
      label: 'Sains & Kode',
      category: 'Sains & Teknologi',
      icon: Cpu,
      tint: 'text-sky-700 dark:text-sky-300 bg-sky-400/20 border-sky-400/40 dark:border-sky-300/30',
    },
    {
      label: 'Stoisisme',
      category: 'Filsafat & Psikologi',
      icon: Compass,
      tint: 'text-violet-700 dark:text-violet-300 bg-violet-400/20 border-violet-400/40 dark:border-violet-300/30',
    },
    {
      label: 'Nusantara',
      category: 'Sejarah & Budaya',
      icon: BookOpen,
      tint: 'text-emerald-700 dark:text-emerald-300 bg-emerald-400/20 border-emerald-400/40 dark:border-emerald-300/30',
    },
  ];

  return (
    <aside
      aria-label="Panel Tag Subjek dan Riwayat Koleksi"
      className="relative w-full xl:w-80 shrink-0 flex flex-col gap-5"
    >
      {/* Decorative Ambient Glacier Blurred Glass Shapes on the Right Side */}
      <div
        aria-hidden="true"
        className="pointer-events-none absolute -top-6 -right-6 w-36 h-36 rounded-full bg-cyan-400/25 dark:bg-sky-400/20 blur-2xl"
      />
      <div
        aria-hidden="true"
        className="pointer-events-none absolute top-1/2 -right-8 w-44 h-44 rounded-3xl rotate-12 bg-indigo-400/20 dark:bg-cyan-500/15 blur-2xl"
      />
      <div
        aria-hidden="true"
        className="pointer-events-none absolute bottom-12 right-4 w-36 h-36 rounded-full bg-sky-300/20 blur-2xl"
      />

      {/* 1. Top Genre Tags (Transparent Glacier Glass Container & Tile Grid) */}
      <div className="glacier-card relative z-10 rounded-3xl p-5">
        <div className="flex items-center justify-between mb-3.5">
          <h2 className="text-sm font-bold text-slate-900 dark:text-white">Tag Subjek OPAC</h2>
          <div className="glacier-pill w-7 h-7 rounded-xl flex items-center justify-center">
            <Sparkles className="w-3.5 h-3.5 text-amber-500 dark:text-amber-300" />
          </div>
        </div>

        <div className="grid grid-cols-2 gap-2.5">
          {quickTags.map((tag) => {
            const TagIcon = tag.icon;
            return (
              <button
                key={tag.label}
                type="button"
                onClick={() => onNavigateOpacCategory(tag.category)}
                className="glacier-tile group px-2.5 py-2 rounded-2xl text-slate-800 dark:text-slate-100 text-xs font-semibold flex items-center gap-2 transition-all cursor-pointer truncate"
              >
                <span
                  className={`w-6 h-6 rounded-full backdrop-blur-md border flex items-center justify-center shrink-0 ${tag.tint}`}
                >
                  <TagIcon className="w-3 h-3" />
                </span>
                <span className="truncate">{tag.label}</span>
              </button>
            );
          })}
        </div>
      </div>

      {/* 2. Baru Dibaca / Riwayat Koleksi Terkini (Transparent Glacier Glass Card & Rows) */}
      <div className="glacier-card relative z-10 rounded-3xl p-5 flex-1 flex flex-col">
        <div className="flex items-center justify-between gap-2 mb-4">
          <div className="flex items-center gap-2">
            <div className="glacier-pill w-7 h-7 rounded-xl flex items-center justify-center">
              <Clock className="w-3.5 h-3.5 text-amber-500 dark:text-amber-300" />
            </div>
            <h2 className="text-base font-bold text-slate-900 dark:text-white">Baru Dibaca</h2>
          </div>

          <span className="glacier-pill px-2.5 py-1 rounded-xl text-[11px] font-bold text-slate-700 dark:text-slate-300">
            Riwayat
          </span>
        </div>

        {/* Inspiration 2 "Played" -> Riwayat Sesi Baca Terkini (Glacier Tile Rows) */}
        <div className="space-y-2.5">
          {books.slice(0, 5).map((book, idx) => {
            const times = ['4 mnt lalu', '20 mnt lalu', '2 jam lalu', '3 jam lalu', 'Kemarin'];
            return (
              <button
                key={book.id}
                type="button"
                onClick={() => onSelectBook(book.id)}
                className="glacier-tile w-full flex items-center justify-between gap-3 p-2.5 rounded-2xl transition-all text-left cursor-pointer"
              >
                <div className="flex items-center gap-3 min-w-0">
                  <BookCoverArt book={book} size="xs" />
                  <div className="min-w-0">
                    <p className="text-xs font-bold text-slate-900 dark:text-white truncate">
                      {book.titleHighlight}
                    </p>
                    <p className="text-[11px] text-slate-700 dark:text-slate-300 truncate">
                      {book.author}
                    </p>
                  </div>
                </div>
                <span className="glacier-pill px-2 py-0.5 rounded-full text-[10px] font-mono-tabular text-slate-700 dark:text-slate-300 shrink-0">
                  {times[idx]}
                </span>
              </button>
            );
          })}
        </div>
      </div>

      {/* 3. Bottom Featured Recommendation Card (Transparent Glacier Glass Card) */}
      <div className="glacier-card relative z-10 rounded-3xl overflow-hidden text-slate-900 dark:text-white p-4">
        <div
          onClick={() => onSelectBook(spotlightBook.id)}
          role="button"
          tabIndex={0}
          onKeyDown={(e) => {
            if (e.key === 'Enter' || e.key === ' ') onSelectBook(spotlightBook.id);
          }}
          className="relative h-36 rounded-2xl overflow-hidden mb-3 border border-sky-200/30 cursor-pointer group"
        >
          <BookCoverArt book={spotlightBook} size="banner" />
          <div className="glacier-pill absolute top-2.5 right-2.5 px-2.5 py-1 rounded-full text-[10px] font-mono-tabular font-bold text-slate-900 dark:text-white">
            Rekomendasi OPAC
          </div>
        </div>
        <div className="flex items-center justify-between gap-2 px-1">
          <div className="min-w-0">
            <p className="text-xs font-bold text-slate-900 dark:text-white truncate">
              {spotlightBook.title}
            </p>
            <p className="text-[11px] text-slate-700 dark:text-indigo-200 truncate">
              {spotlightBook.author} · {spotlightBook.availableCopies} eks tersedia
            </p>
          </div>
          <button
            type="button"
            onClick={() => onBorrowBook(spotlightBook.id)}
            disabled={spotlightBook.availableCopies <= 0}
            aria-label={`Pinjam cepat ${spotlightBook.title}`}
            className="glacier-pill w-9 h-9 rounded-full hover:bg-[#FF6B35] hover:text-white disabled:opacity-40 text-slate-900 dark:text-white flex items-center justify-center transition-all cursor-pointer shrink-0 shadow-sm"
            title="Tambah ke Tote Bag Peminjaman"
          >
            <Plus className="w-4 h-4" />
          </button>
        </div>
      </div>
    </aside>
  );
};
