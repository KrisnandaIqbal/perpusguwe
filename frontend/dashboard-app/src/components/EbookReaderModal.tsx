import React, { useState } from 'react';
import {
  X,
  BookOpen,
  ChevronLeft,
  ChevronRight,
  CheckCircle2,
  Bookmark,
  Type,
} from 'lucide-react';
import { type BookItem } from '../utils/libraryLogic';

interface EbookReaderModalProps {
  book: BookItem;
  isOpen: boolean;
  onClose: () => void;
  onCompleteReadingSession: (pagesRead: number) => void;
}

export const EbookReaderModal: React.FC<EbookReaderModalProps> = ({
  book,
  isOpen,
  onClose,
  onCompleteReadingSession,
}) => {
  const [chapterIndex, setChapterIndex] = useState(0);
  const [fontSize, setFontSize] = useState<'sm' | 'base' | 'lg'>('base');
  const [readerTheme, setReaderTheme] = useState<'dark' | 'sepia' | 'light'>('dark');

  if (!isOpen) return null;

  const chapters = book.ebookChapters?.length
    ? book.ebookChapters
    : [
        {
          chapterNumber: 1,
          title: 'Bab I — Edisi Digital Perpustakaan',
          pagesRange: 'Halaman 1–20',
          content: [book.synopsis, book.extendedSynopsis || ''],
        },
      ];

  const currentChapter = chapters[chapterIndex] || chapters[0];

  const themeClasses = {
    dark: 'bg-[#15132B] text-slate-100 border-white/15',
    sepia: 'bg-[#F5ECD7] text-[#2C221E] border-amber-900/20',
    light: 'bg-white text-slate-900 border-slate-200',
  }[readerTheme];

  const fontSizeClasses = {
    sm: 'text-sm leading-relaxed',
    base: 'text-base leading-loose',
    lg: 'text-lg leading-loose',
  }[fontSize];

  return (
    <div
      role="dialog"
      aria-modal="true"
      aria-labelledby="ebook-reader-title"
      className="fixed inset-0 z-50 bg-black/75 backdrop-blur-sm flex items-center justify-center p-3 sm:p-6"
    >
      <div
        className={`w-full max-w-4xl rounded-3xl border shadow-2xl overflow-hidden flex flex-col max-h-[90vh] ${themeClasses}`}
      >
        {/* Top Reader Toolbar */}
        <div className="px-6 py-4 border-b border-current/10 flex flex-wrap items-center justify-between gap-4">
          <div className="flex items-center gap-3 min-w-0">
            <div className="w-9 h-9 rounded-xl bg-indigo-600 text-white flex items-center justify-center shrink-0">
              <BookOpen className="w-4 h-4" />
            </div>
            <div className="min-w-0">
              <h2 id="ebook-reader-title" className="text-sm sm:text-base font-bold truncate">
                {book.title} — E-Book Reader
              </h2>
              <p className="text-xs opacity-70 truncate">
                {book.author} · {book.publisher} · {currentChapter.pagesRange}
              </p>
            </div>
          </div>

          {/* Font Size & Theme Controls */}
          <div className="flex items-center gap-2">
            <div className="inline-flex items-center gap-1 px-2 py-1 rounded-xl bg-black/10 dark:bg-white/10 text-xs font-bold">
              <Type className="w-3.5 h-3.5 mr-1 opacity-70" />
              <button
                type="button"
                onClick={() => setFontSize('sm')}
                className={`px-2 py-0.5 rounded-lg cursor-pointer ${
                  fontSize === 'sm' ? 'bg-indigo-600 text-white' : 'opacity-75'
                }`}
              >
                A-
              </button>
              <button
                type="button"
                onClick={() => setFontSize('base')}
                className={`px-2 py-0.5 rounded-lg cursor-pointer ${
                  fontSize === 'base' ? 'bg-indigo-600 text-white' : 'opacity-75'
                }`}
              >
                A
              </button>
              <button
                type="button"
                onClick={() => setFontSize('lg')}
                className={`px-2 py-0.5 rounded-lg cursor-pointer ${
                  fontSize === 'lg' ? 'bg-indigo-600 text-white' : 'opacity-75'
                }`}
              >
                A+
              </button>
            </div>

            <div className="inline-flex items-center gap-1 p-1 rounded-xl bg-black/10 dark:bg-white/10 text-xs font-semibold">
              <button
                type="button"
                onClick={() => setReaderTheme('dark')}
                className={`px-2.5 py-1 rounded-lg cursor-pointer ${
                  readerTheme === 'dark' ? 'bg-indigo-600 text-white' : 'opacity-75'
                }`}
              >
                Gelap
              </button>
              <button
                type="button"
                onClick={() => setReaderTheme('sepia')}
                className={`px-2.5 py-1 rounded-lg cursor-pointer ${
                  readerTheme === 'sepia' ? 'bg-amber-700 text-white' : 'opacity-75'
                }`}
              >
                Sepia
              </button>
              <button
                type="button"
                onClick={() => setReaderTheme('light')}
                className={`px-2.5 py-1 rounded-lg cursor-pointer ${
                  readerTheme === 'light' ? 'bg-slate-800 text-white' : 'opacity-75'
                }`}
              >
                Terang
              </button>
            </div>

            <button
              type="button"
              onClick={onClose}
              aria-label="Tutup E-Book Reader"
              className="p-2 rounded-xl hover:bg-black/10 dark:hover:bg-white/10 transition-colors cursor-pointer"
            >
              <X className="w-5 h-5" />
            </button>
          </div>
        </div>

        {/* Main Body: Chapter Sidebar + Reading Canvas */}
        <div className="flex-1 overflow-y-auto grid grid-cols-1 md:grid-cols-12">
          {/* Chapter List */}
          <div className="md:col-span-4 border-b md:border-b-0 md:border-r border-current/10 p-4 space-y-2">
            <p className="text-xs font-extrabold uppercase tracking-wider opacity-60 px-2 mb-2">
              Daftar Isi E-Book
            </p>
            {chapters.map((ch, idx) => (
              <button
                key={ch.chapterNumber}
                type="button"
                onClick={() => setChapterIndex(idx)}
                className={`w-full text-left p-3 rounded-2xl transition-colors cursor-pointer ${
                  idx === chapterIndex
                    ? 'bg-indigo-600 text-white font-bold shadow-sm'
                    : 'hover:bg-black/5 dark:hover:bg-white/5 opacity-85'
                }`}
              >
                <p className="text-xs font-bold line-clamp-1">{ch.title}</p>
                <p className="text-[11px] opacity-75 font-mono-tabular mt-0.5">{ch.pagesRange}</p>
              </button>
            ))}
          </div>

          {/* Reading Text Canvas */}
          <div className="md:col-span-8 p-6 sm:p-8 space-y-5 overflow-y-auto">
            <div className="flex items-center justify-between text-xs font-mono-tabular opacity-60">
              <span>{book.ddcCode}</span>
              <span>{currentChapter.pagesRange}</span>
            </div>

            <h3 className="text-xl sm:text-2xl font-serif-editorial font-bold leading-snug">
              {currentChapter.title}
            </h3>

            <div className={`space-y-4 font-serif-editorial ${fontSizeClasses}`}>
              {currentChapter.content.map((para, i) => (
                <p key={i} className="leading-relaxed">
                  {para}
                </p>
              ))}
            </div>
          </div>
        </div>

        {/* Bottom Navigation Bar */}
        <div className="px-6 py-4 border-t border-current/10 flex flex-wrap items-center justify-between gap-3">
          <div className="flex items-center gap-2">
            <button
              type="button"
              disabled={chapterIndex === 0}
              onClick={() => setChapterIndex((i) => Math.max(0, i - 1))}
              className="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-black/10 dark:bg-white/10 disabled:opacity-40 text-xs font-bold cursor-pointer"
            >
              <ChevronLeft className="w-4 h-4" />
              <span>Bab Sebelumnya</span>
            </button>
            <button
              type="button"
              disabled={chapterIndex >= chapters.length - 1}
              onClick={() => setChapterIndex((i) => Math.min(chapters.length - 1, i + 1))}
              className="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-black/10 dark:bg-white/10 disabled:opacity-40 text-xs font-bold cursor-pointer"
            >
              <span>Bab Berikutnya</span>
              <ChevronRight className="w-4 h-4" />
            </button>
          </div>

          <button
            type="button"
            onClick={() => {
              onCompleteReadingSession(18);
              onClose();
            }}
            className="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow-md transition-colors cursor-pointer"
          >
            <CheckCircle2 className="w-4 h-4" />
            <span>Simpan Progres Baca (+18 Hal) & Tutup</span>
          </button>
        </div>
      </div>
    </div>
  );
};
