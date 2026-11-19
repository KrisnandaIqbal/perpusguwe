import React from 'react';
import { Mail, MessageSquare, AlertCircle, ShoppingBag, Bookmark, BookOpen } from 'lucide-react';
import { type BookItem } from '../utils/libraryLogic';

interface RightSideBarProps {
  activeMenu: string;
  selectedBook?: BookItem;
}

export const RightSideBar: React.FC<RightSideBarProps> = ({ activeMenu, selectedBook }) => {
  return (
    <aside className="w-full shrink-0 space-y-6">
      {activeMenu === 'book-detail' && selectedBook && (
        <div className="rounded-2xl p-5 border border-slate-200 bg-white shadow-sm space-y-4">
          <div className="flex items-center gap-2 text-slate-800 font-bold">
            <BookOpen className="w-5 h-5 text-sky-600" />
            <span>Status Ketersediaan</span>
          </div>
          <div className="space-y-2">
            <p className="text-sm text-slate-700">Stok Fisik: <span className="font-bold text-emerald-600">{selectedBook.availableCopies}</span> tersedia</p>
            <p className="text-sm text-slate-700">Lokasi: <span className="font-semibold text-slate-700">{selectedBook.shelfLocation}</span></p>
          </div>
          <button className="w-full py-2.5 bg-sky-600 hover:bg-sky-700 text-white rounded-lg font-bold text-sm cursor-pointer transition-colors flex items-center justify-center gap-2">
            <ShoppingBag className="w-4 h-4" /> Pinjam Buku
          </button>
          <button className="w-full py-2.5 border border-slate-200 hover:bg-slate-50 text-slate-700 rounded-lg font-bold text-sm cursor-pointer transition-colors flex items-center justify-center gap-2">
            <Bookmark className="w-4 h-4" /> Simpan ke Favorit
          </button>
        </div>
      )}

      {/* Info Boxes */}
      <div className="rounded-2xl p-5 border border-slate-200 bg-white shadow-sm space-y-4">
        <div className="flex items-center gap-2 text-slate-800 font-bold">
          <AlertCircle className="w-5 h-5 text-sky-600" />
          <span>Info</span>
        </div>
        <p className="text-sm text-slate-700">
          Pantau Instagram @perpusjkt untuk tahu event yang akan datang
        </p>
        <button className="w-full py-2.5 bg-orange-600 hover:bg-orange-700 text-white rounded-lg font-bold text-sm cursor-pointer transition-colors">
          Reservasi Event
        </button>
      </div>

      <div className="rounded-2xl p-5 border border-slate-200 bg-white shadow-sm space-y-4">
        <div className="flex items-center gap-2 text-slate-800 font-bold">
          <AlertCircle className="w-5 h-5 text-sky-600" />
          <span>Info</span>
        </div>
        <p className="text-sm text-slate-700">
          Tidak menemukan buku yang kamu cari?
        </p>
        <button className="w-full py-2.5 bg-sky-700 hover:bg-sky-800 text-white rounded-lg font-bold text-sm cursor-pointer transition-colors">
          Usul Buku
        </button>
      </div>

      {/* Language Switcher */}
      <div className="rounded-2xl p-5 border border-slate-200 bg-white shadow-sm space-y-3">
        <h3 className="text-slate-800 font-bold">Ganti Bahasa</h3>
        <div className="flex gap-2">
          <button className="flex-1 py-2 border border-sky-600 bg-sky-50 text-sky-700 rounded-lg font-bold text-sm cursor-pointer">ID</button>
          <button className="flex-1 py-2 border border-slate-200 text-slate-700 hover:bg-slate-50 rounded-lg font-bold text-sm cursor-pointer">EN</button>
        </div>
      </div>

      {/* Hubungi Kami */}
      <div className="rounded-2xl p-5 border border-slate-200 bg-white shadow-sm space-y-3">
        <h3 className="text-slate-800 font-bold">Hubungi Kami</h3>
        <button className="w-full py-2.5 border border-slate-200 hover:bg-slate-50 rounded-lg flex items-center justify-center gap-2 text-sm font-bold text-slate-700 cursor-pointer transition-colors">
          <Mail className="w-4 h-4" /> Email
        </button>
        <button className="w-full py-2.5 border border-slate-200 hover:bg-slate-50 rounded-lg flex items-center justify-center gap-2 text-sm font-bold text-slate-700 cursor-pointer transition-colors">
          <MessageSquare className="w-4 h-4" /> WhatsApp
        </button>
      </div>
    </aside>
  );
};
