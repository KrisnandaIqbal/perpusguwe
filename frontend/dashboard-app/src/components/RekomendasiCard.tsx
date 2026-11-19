import React from 'react';
import { BookOpen, MapPin } from 'lucide-react';
import { BookCoverArt } from './BookCoverArt';

export interface BukuRekomendasi {
  id: number;
  url: string;
  judul: string;
  pengarang: string;
  penerbit: string;
  tahun: string;
  subjek: string;
  cover: string;
  ddc: string;
  tersedia: number;
  total: number;
  lokasi: string;
}

// Tailwind hanya membuat kelas yang tertulis utuh, jadi gradien sampul sengaja ditulis penuh di sini.
const GRADIEN = [
  { gradient: 'from-sky-700 via-sky-900 to-slate-950', accent: '#38BDF8' },
  { gradient: 'from-amber-500 via-orange-600 to-rose-950', accent: '#FBBF24' },
  { gradient: 'from-emerald-600 via-emerald-800 to-slate-950', accent: '#34D399' },
  { gradient: 'from-violet-600 via-indigo-800 to-slate-950', accent: '#A78BFA' },
  { gradient: 'from-rose-600 via-red-800 to-slate-950', accent: '#FB7185' },
];

export const RekomendasiCard: React.FC<{ buku: BukuRekomendasi }> = ({ buku }) => {
  const ada = buku.tersedia > 0;
  const warna = GRADIEN[buku.id % GRADIEN.length];

  return (
    <a
      href={buku.url}
      className="group relative bg-white border border-slate-200/90 hover:border-sky-500/80 rounded-2xl overflow-hidden flex flex-col transition-all duration-300 hover:-translate-y-1 hover:shadow-[0_14px_30px_-8px_rgba(14,165,233,0.18)] focus-visible:outline-2 focus-visible:outline-sky-600"
    >
      <div className="relative aspect-[4/4.2] bg-gradient-to-b from-slate-50 via-slate-100/75 to-slate-200/60 flex items-center justify-center p-3 overflow-hidden">
        <div
          aria-hidden="true"
          className="pointer-events-none absolute w-28 h-28 rounded-full blur-2xl opacity-35 group-hover:opacity-60 transition-opacity duration-300"
          style={{ backgroundColor: warna.accent }}
        />
        <div
          className={`absolute top-2.5 left-0 z-20 pl-2 pr-2.5 py-0.5 rounded-r-full text-[10px] font-extrabold tracking-wide text-white shadow-sm ${
            ada ? 'bg-gradient-to-r from-rose-600 to-orange-500' : 'bg-slate-600'
          }`}
        >
          {buku.total > 0 ? (ada ? `Tersedia ${buku.tersedia}/${buku.total}` : 'Dipinjam semua') : 'Tersedia'}
        </div>

        <div className="relative z-10 transition-transform duration-300 group-hover:scale-105 group-hover:-rotate-1">
          {buku.cover ? (
            <img
              src={buku.cover}
              alt={`Sampul ${buku.judul}`}
              loading="lazy"
              className="w-24 h-33 object-cover rounded-lg shadow-[0_8px_20px_rgba(15,23,42,0.3)] ring-1 ring-black/10"
            />
          ) : (
            <BookCoverArt
              book={{
                titleHighlight: buku.judul,
                titleItalic: '',
                author: buku.pengarang,
                ddcCode: buku.ddc,
                coverGradient: warna.gradient,
                coverAccent: warna.accent,
                coverPattern: 'editorial',
              }}
              size="md"
              className="w-24 h-33 rounded-lg shadow-[0_8px_20px_rgba(15,23,42,0.3)] ring-1 ring-black/10"
            />
          )}
        </div>
      </div>

      <div className="p-3 flex-1 flex flex-col gap-2 bg-white">
        <div className="flex items-center justify-between gap-1 text-xs font-semibold">
          <span className="truncate text-sky-700 font-bold">{buku.subjek || 'Koleksi perpustakaan'}</span>
          <span className="font-mono-tabular text-slate-600 shrink-0">{buku.tahun}</span>
        </div>
        <h3 className="text-sm font-bold text-slate-900 group-hover:text-sky-700 line-clamp-3 leading-snug transition-colors">
          {buku.judul}
        </h3>
        <p className="text-xs font-semibold text-slate-700 truncate flex items-center gap-1">
          <BookOpen className="w-3 h-3 shrink-0" aria-hidden="true" />
          <span className="truncate">{buku.pengarang || buku.penerbit || '-'}</span>
        </p>
        <p className="mt-auto text-xs font-bold text-orange-700 flex items-center gap-1 truncate">
          <MapPin className="w-3 h-3 shrink-0" aria-hidden="true" />
          <span className="truncate">{buku.lokasi}</span>
        </p>
      </div>
    </a>
  );
};
