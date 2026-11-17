import React, { useState } from 'react';
import {
  Search,
  BookOpen,
  MapPin,
  Users,
  Plus,
  Filter,
  Star,
} from 'lucide-react';
import {
  type BookItem,
  type Member,
  type CirculationRecord,
  filterOpacBooks,
  canBorrowBook,
} from '../utils/libraryLogic';
import { BookCoverArt } from './BookCoverArt';
import { BookGridCard } from './BookGridCard';

interface OpacTabProps {
  books: BookItem[];
  activeMember: Member;
  circulation: CirculationRecord[];
  selectedBookId: string;
  bookmarkedIds?: string[];
  toteBagIds?: string[];
  onSelectBook: (bookId: string) => void;
  onOpenBookDetail: (bookId: string) => void;
  onBorrowBook: (bookId: string) => void;
  onToggleBookmark?: (bookId: string) => void;
  onAddToToteBag?: (bookId: string) => void;
  onAddBook: (newBook: Omit<BookItem, 'id'>) => void;
  globalSearchQuery?: string;
}

const CATEGORIES: ('Semua' | BookItem['category'])[] = [
  'Semua',
  'Fiksi & Sastra',
  'Sains & Teknologi',
  'Filsafat & Psikologi',
  'Sejarah & Budaya',
  'Seni & Desain',
];

export const OpacTab: React.FC<OpacTabProps> = ({
  books,
  activeMember,
  circulation,
  selectedBookId,
  bookmarkedIds = [],
  toteBagIds = [],
  onSelectBook,
  onOpenBookDetail,
  onBorrowBook,
  onToggleBookmark,
  onAddToToteBag,
  onAddBook,
  globalSearchQuery = '',
}) => {
  const [localQuery, setLocalQuery] = useState('');
  const [selectedCategory, setSelectedCategory] = useState<'Semua' | BookItem['category']>('Semua');
  const [onlyAvailable, setOnlyAvailable] = useState(false);
  const [isAddBookModalOpen, setIsAddBookModalOpen] = useState(false);

  const [newTitle, setNewTitle] = useState('');
  const [newAuthor, setNewAuthor] = useState('');
  const [newIsbn, setNewIsbn] = useState('');
  const [newDdc, setNewDdc] = useState('');
  const [newShelf, setNewShelf] = useState('');
  const [newCategory, setNewCategory] = useState<BookItem['category']>('Sains & Teknologi');
  const [newPages, setNewPages] = useState('320');
  const [newCopies, setNewCopies] = useState('4');
  const [formError, setFormError] = useState<string | null>(null);

  const effectiveQuery = localQuery || globalSearchQuery;
  const filteredBooks = filterOpacBooks(books, effectiveQuery, selectedCategory, onlyAvailable);

  const inspectBook = books.find((b) => b.id === selectedBookId) || filteredBooks[0] || books[0];
  const borrowCheck = canBorrowBook(activeMember, inspectBook, circulation);

  const handleCreateBook = (e: React.FormEvent) => {
    e.preventDefault();
    if (!newTitle.trim() || !newAuthor.trim() || !newDdc.trim()) {
      setFormError('Judul buku, nama penulis, dan nomor panggil DDC wajib diisi.');
      return;
    }
    const words = newTitle.trim().split(' ');
    const highlight = words.slice(0, Math.min(2, words.length)).join(' ');
    const italicPart = words.slice(Math.min(2, words.length)).join(' ') || 'Edisi Koleksi Utama';

    const copiesNum = Math.max(1, parseInt(newCopies, 10) || 3);
    const pagesNum = Math.max(50, parseInt(newPages, 10) || 250);
    const shelfText = newShelf.trim() || 'Lantai 2 · Rak Koleksi Baru B-04';
    const ddcText = newDdc.trim();

    onAddBook({
      title: newTitle.trim(),
      titleHighlight: highlight,
      titleItalic: italicPart,
      author: newAuthor.trim(),
      publisher: 'Penerbit Akademika Nusantara',
      year: 2026,
      isbn: newIsbn.trim() || '978-602-8811-99-2',
      ddcCode: ddcText,
      shelfLocation: shelfText,
      category: newCategory,
      formats: ['Fisik', 'E-Book'],
      totalPages: pagesNum,
      availableCopies: copiesNum,
      totalCopies: copiesNum,
      rating: 4.8,
      readersCount: 1240,
      borrowedCount: 185,
      avgReadTimeMinutes: Math.round(pagesNum * 0.85),
      synopsis: `Koleksi katalog OPAC kategori ${newCategory} karya ${newAuthor.trim()}. Tersedia untuk peminjaman anggota dan baca di tempat.`,
      extendedSynopsis: `Koleksi katalog OPAC kategori ${newCategory} karya ${newAuthor.trim()} ini menyajikan pembahasan mendalam yang relevan bagi mahasiswa, peneliti, dan pembaca umum.\n\nTersedia baik dalam bentuk buku cetak fisik di rak perpustakaan maupun format E-Book interaktif yang dapat dibaca langsung di aplikasi BiblioVerse.`,
      branches: [],
      ebookChapters: [],
      coverGradient: 'from-indigo-600 via-violet-700 to-slate-950',
      coverAccent: '#818CF8',
      coverPattern: 'constellation',
    });

    setNewTitle(''); setNewAuthor(''); setNewIsbn(''); setNewDdc(''); setNewShelf(''); setFormError(null); setIsAddBookModalOpen(false);
  };

  return (
    <div className="space-y-6">
      {/* Hero Banner */}
      <div className="bg-slate-900 text-white rounded-3xl p-7 flex flex-col lg:flex-row justify-between gap-6">
        <div className="space-y-3 max-w-xl">
          <p className="text-xs font-semibold text-sky-400">KOLEKSI TRENDING OPAC</p>
          <h1 className="text-3xl font-extrabold tracking-tight">{inspectBook.title}</h1>
          <p className="text-sm text-slate-300 leading-relaxed line-clamp-2">{inspectBook.synopsis}</p>
          <div className="pt-2 flex flex-wrap gap-3">
            <button disabled={!borrowCheck.allowed} onClick={() => onBorrowBook(inspectBook.id)} className="px-5 py-2 rounded-xl bg-sky-600 hover:bg-sky-700 disabled:opacity-50 text-white text-sm font-bold transition cursor-pointer">
              {borrowCheck.allowed ? 'Pinjam Buku' : 'Tidak Tersedia'}
            </button>
            <button onClick={() => { const el = document.getElementById('opac-shelf-grid'); el?.scrollIntoView({ behavior: 'smooth' }); }} className="px-5 py-2 rounded-xl bg-slate-700 hover:bg-slate-600 text-white text-sm font-bold transition cursor-pointer">
              Jelajahi Rak
            </button>
          </div>
        </div>
        <div className="flex items-center gap-4 p-4 bg-white/10 rounded-2xl shrink-0">
          <BookCoverArt book={inspectBook} size="md" />
          <div className="text-sm space-y-1">
             <p className="font-bold">{inspectBook.author}</p>
             <p className="text-slate-300">{inspectBook.shelfLocation}</p>
             <p className="font-semibold text-emerald-300">Stok: {inspectBook.availableCopies}/{inspectBook.totalCopies}</p>
          </div>
        </div>
      </div>

      {/* Filter Bar */}
      <div id="opac-shelf-grid" className="bg-white border border-slate-200 rounded-3xl p-5 space-y-4">
        <div className="flex flex-col md:flex-row gap-4">
            <div className="relative flex-1">
                <Search className="absolute left-3 top-2.5 w-4 h-4 text-slate-400" />
                <input type="text" value={localQuery} onChange={(e) => setLocalQuery(e.target.value)} placeholder="Cari buku..." className="w-full pl-10 pr-4 py-2 rounded-xl bg-slate-50 border border-slate-200 text-sm focus:ring-2 focus:ring-sky-500" />
            </div>
            <label className="flex items-center gap-2 text-sm text-slate-700">
                <input type="checkbox" checked={onlyAvailable} onChange={(e) => setOnlyAvailable(e.target.checked)} className="rounded border-slate-300" />
                Hanya Tersedia
            </label>
        </div>
        <div className="flex flex-wrap gap-2">
           {CATEGORIES.map(cat => (
               <button key={cat} onClick={() => setSelectedCategory(cat)} className={`px-4 py-1.5 rounded-full text-xs font-semibold ${selectedCategory === cat ? 'bg-sky-600 text-white' : 'bg-slate-100 hover:bg-slate-200'}`}>
                   {cat}
               </button>
           ))}
        </div>
      </div>

      {/* Book Grid */}
      <div className="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
        {filteredBooks.map(book => (
          <BookGridCard
            key={book.id}
            book={book}
            onClick={onOpenBookDetail}
            isBookmarked={bookmarkedIds.includes(book.id)}
            isInToteBag={toteBagIds.includes(book.id)}
            onToggleBookmark={onToggleBookmark}
            onAddToToteBag={onAddToToteBag}
          />
        ))}
      </div>
    </div>
  );
};
