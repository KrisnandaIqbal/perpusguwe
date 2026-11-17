import React, { useState } from 'react';
import {
  ShieldCheck,
  Sparkles,
  QrCode,
  Calendar,
  BookOpen,
  CheckCircle2,
  Bookmark,
  UserCheck,
  Clock,
  Mail,
  Phone,
  Lock,
  History,
  MapPin,
  Gift,
  HelpCircle,
  FileText,
  Globe,
  ChevronRight,
  ChevronDown,
  Star,
  Plus,
  Send,
} from 'lucide-react';
import {
  type Member,
  type BookItem,
  type CirculationRecord,
  type TransactionRecord,
  calculateDaysRemaining,
  calculateReadingProgress,
} from '../utils/libraryLogic';
import { BookCoverArt } from './BookCoverArt';

interface ProfilePageProps {
  activeMember: Member;
  books: BookItem[];
  circulation: CirculationRecord[];
  transactions: TransactionRecord[];
  bookmarkedIds: string[];
  referenceDate: string;
  onOpenDigitalCard: () => void;
  onOpenBookDetail: (bookId: string) => void;
  onNavigateTab: (tab: 'overview' | 'members' | 'opac' | 'circulation' | 'transactions') => void;
  onOpenSupportModal: () => void;
}

type ActiveSettingPanel =
  | 'biodata'
  | 'borrowed'
  | 'contact'
  | 'security'
  | 'history-circulation'
  | 'history-reservation'
  | 'history-donation'
  | 'privacy'
  | null;

interface BranchReservationItem {
  id: string;
  code: string;
  branchName: string;
  seatZone: string;
  visitDate: string;
  timeSlot: string;
  status: 'Terjadwal' | 'Selesai Hadir';
}

interface BookProposalOrDonationItem {
  id: string;
  type: 'Usulan Koleksi' | 'Donasi Buku';
  title: string;
  author: string;
  submittedDate: string;
  status: 'Disetujui & Masuk Rak' | 'Dalam Kurasi Pustakawan' | 'Diterima Cabang Pusat';
}

export const ProfilePage: React.FC<ProfilePageProps> = ({
  activeMember,
  books,
  circulation,
  transactions,
  bookmarkedIds,
  referenceDate,
  onOpenDigitalCard,
  onOpenBookDetail,
  onNavigateTab,
  onOpenSupportModal,
}) => {
  // Interactive expandable panel state (defaults to 'borrowed' so user immediately sees rich content, and can click any menu item)
  const [openPanel, setOpenPanel] = useState<ActiveSettingPanel>('borrowed');
  const [feedbackBanner, setFeedbackBanner] = useState<string | null>(null);

  // Biodata & Contact editable state
  const [nppInput, setNppInput] = useState(activeMember.nppNumber || 'NPP-3173-0102-2026');
  const [nikInput, setNikInput] = useState(activeMember.nikId || '3173042608990001');
  const [departmentInput, setDepartmentInput] = useState(activeMember.department);
  const [addressInput, setAddressInput] = useState(
    activeMember.address || 'Jl. Taman Literasi Raya No. 24, Kebayoran Baru, Jakarta Selatan'
  );
  const [emailInput, setEmailInput] = useState(activeMember.email);
  const [phoneInput, setPhoneInput] = useState(activeMember.phone);

  // Security state
  const [oldPassword, setOldPassword] = useState('');
  const [newPassword, setNewPassword] = useState('');
  const [twoFactorEnabled, setTwoFactorEnabled] = useState(true);

  // Review modal / inline state for returned books
  const [reviewingLoanId, setReviewingLoanId] = useState<string | null>(null);
  const [reviewRating, setReviewRating] = useState(5);
  const [reviewComment, setReviewComment] = useState('');
  const [submittedLoanReviews, setSubmittedLoanReviews] = useState<Record<string, string>>({});

  // Branch Reservations state
  const [reservations, setReservations] = useState<BranchReservationItem[]>([
    {
      id: 'res-1',
      code: 'RSV-2026-1112',
      branchName: 'Perpustakaan Pusat Utama',
      seatZone: 'Lantai 3 · Zona Baca Hening Kursi #B14',
      visitDate: '12 November 2026',
      timeSlot: '09:00 – 15:00 WIB',
      status: 'Terjadwal',
    },
    {
      id: 'res-2',
      code: 'RSV-2026-1028',
      branchName: 'Cabang Taman Literasi Kota',
      seatZone: 'Paviliun Diskusi · Meja Kolaborasi #04',
      visitDate: '28 Oktober 2026',
      timeSlot: '13:00 – 18:00 WIB',
      status: 'Selesai Hadir',
    },
  ]);

  // Proposals & Donations state
  const [proposals, setProposals] = useState<BookProposalOrDonationItem[]>([
    {
      id: 'prop-1',
      type: 'Usulan Koleksi',
      title: 'Clean Architecture: A Craftsman’s Guide to Software Structure',
      author: 'Robert C. Martin',
      submittedDate: '02 November 2026',
      status: 'Disetujui & Masuk Rak',
    },
    {
      id: 'prop-2',
      type: 'Donasi Buku',
      title: 'Laut Bercerita (Cetakan Hardcover Edisi Khusus)',
      author: 'Leila S. Chudori',
      submittedDate: '19 Oktober 2026',
      status: 'Diterima Cabang Pusat',
    },
  ]);
  const [newProposalTitle, setNewProposalTitle] = useState('');
  const [newProposalAuthor, setNewProposalAuthor] = useState('');
  const [newProposalType, setNewProposalType] = useState<'Usulan Koleksi' | 'Donasi Buku'>(
    'Usulan Koleksi'
  );

  // Footer language state
  const [appLanguage, setAppLanguage] = useState<'ID' | 'EN'>('ID');

  const triggerFeedback = (msg: string) => {
    setFeedbackBanner(msg);
    setTimeout(() => setFeedbackBanner(null), 3500);
  };

  // Member metrics calculation
  const memberAllCirculation = circulation.filter((c) => c.memberId === activeMember.id);
  const memberActiveLoans = memberAllCirculation.filter((c) => c.status !== 'Dikembalikan');
  const memberReturnedLoans = memberAllCirculation.filter((c) => c.status === 'Dikembalikan');

  const unpaidFines = transactions.filter(
    (t) => t.memberId === activeMember.id && t.status === 'Menunggu Pembayaran'
  );
  const overdueLoans = memberActiveLoans.filter((l) => l.status === 'Terlambat');
  const violationCount = unpaidFines.length + overdueLoans.length;

  const totalTransactionsCount = Math.max(4, memberAllCirculation.length);
  const activeBorrowedCount = memberActiveLoans.length;

  const joinDateDisplay = activeMember.joinDateFormatted || '26 Agustus 2026';
  const expiryDateDisplay = activeMember.expiryDateFormatted || '25 Agustus 2028';

  const toggleSection = (panel: ActiveSettingPanel) => {
    setOpenPanel((prev) => (prev === panel ? null : panel));
  };

  const handleSaveBiodata = (e: React.FormEvent) => {
    e.preventDefault();
    triggerFeedback('Biodata diri (NPP, NIK/ID, dan alamat instansi) berhasil diperbarui.');
  };

  const handleSaveContact = (e: React.FormEvent) => {
    e.preventDefault();
    triggerFeedback(`Kontak terverifikasi (${emailInput} & ${phoneInput}) berhasil disimpan.`);
  };

  const handleSaveSecurity = (e: React.FormEvent) => {
    e.preventDefault();
    setOldPassword('');
    setNewPassword('');
    triggerFeedback('Kata sandi dan pengaturan keamanan akun berhasil diperbarui.');
  };

  const handlePublishReview = (loan: CirculationRecord) => {
    if (!reviewComment.trim()) return;
    setSubmittedLoanReviews((prev) => ({
      ...prev,
      [loan.id]: `${reviewRating}★ — "${reviewComment.trim()}"`,
    }));
    setReviewingLoanId(null);
    setReviewComment('');
    triggerFeedback(`Ulasan Anda untuk "${loan.bookTitle}" telah diterbitkan!`);
  };

  const handleAddQuickReservation = () => {
    const newRes: BranchReservationItem = {
      id: `res-${Date.now()}`,
      code: `RSV-2026-${Math.floor(1200 + Math.random() * 800)}`,
      branchName: 'Perpustakaan Riset & Digital',
      seatZone: 'Lantai 2 · Ruang Baca Individual #A08',
      visitDate: '16 November 2026',
      timeSlot: '10:00 – 16:00 WIB',
      status: 'Terjadwal',
    };
    setReservations((prev) => [newRes, ...prev]);
    triggerFeedback(`Reservasi kunjungan cabang (${newRes.code}) berhasil dijadwalkan!`);
  };

  const handleAddProposal = (e: React.FormEvent) => {
    e.preventDefault();
    if (!newProposalTitle.trim() || !newProposalAuthor.trim()) return;
    const item: BookProposalOrDonationItem = {
      id: `prop-${Date.now()}`,
      type: newProposalType,
      title: newProposalTitle.trim(),
      author: newProposalAuthor.trim(),
      submittedDate: '09 November 2026',
      status: 'Dalam Kurasi Pustakawan',
    };
    setProposals((prev) => [item, ...prev]);
    setNewProposalTitle('');
    setNewProposalAuthor('');
    triggerFeedback(`${newProposalType} "${item.title}" berhasil dikirim ke tim kurasi pustakawan.`);
  };

  return (
    <div className="space-y-6 pb-8">
      {/* Top Notification Toast */}
      {feedbackBanner && (
        <div
          role="status"
          className="p-3.5 rounded-2xl bg-emerald-500/20 backdrop-blur-xl border border-emerald-400/40 text-emerald-950 dark:text-emerald-200 text-xs font-bold flex items-center justify-between gap-3 shadow-sm"
        >
          <div className="flex items-center gap-2">
            <CheckCircle2 className="w-4 h-4 text-emerald-500 dark:text-emerald-300 shrink-0" />
            <span>{feedbackBanner}</span>
          </div>
          <button
            type="button"
            onClick={() => setFeedbackBanner(null)}
            className="text-[11px] underline cursor-pointer"
          >
            Tutup
          </button>
        </div>
      )}

      {/* A. HEADER IDENTITAS & STATUS ANGGOTA (Transparent Glacier Glass) */}
      <section
        aria-label="Header Identitas dan Status Anggota"
        className="glacier-card relative overflow-hidden rounded-3xl p-6 sm:p-7 text-slate-900 dark:text-white"
      >
        <div
          aria-hidden="true"
          className="pointer-events-none absolute -top-20 right-10 w-64 h-64 rounded-full bg-cyan-400/25 blur-3xl"
        />
        <div
          aria-hidden="true"
          className="pointer-events-none absolute -bottom-24 left-12 w-60 h-60 rounded-full bg-indigo-400/20 blur-3xl"
        />

        <div className="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
          {/* Avatar + Full Name + Verified & Tier Badges + Validity Dates */}
          <div className="flex flex-col sm:flex-row sm:items-center gap-5">
            <div className="relative shrink-0">
              <div
                className={`w-20 h-20 sm:w-24 sm:h-24 rounded-3xl bg-gradient-to-br ${activeMember.avatarBg} flex items-center justify-center text-white font-extrabold text-2xl sm:text-3xl ring-4 ring-white/25 shadow-2xl`}
              >
                {activeMember.avatarInitials}
              </div>
              <span
                title="Anggota Terverifikasi"
                className="absolute -bottom-1.5 -right-1.5 w-7 h-7 rounded-full bg-emerald-500 border-2 border-slate-900 flex items-center justify-center shadow-md"
              >
                <ShieldCheck className="w-4 h-4 text-white" />
              </span>
            </div>

            <div className="space-y-2">
              {/* Membership Status Badges */}
              <div className="flex flex-wrap items-center gap-2">
                <span
                  data-testid="profile-verified-badge"
                  className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500/20 border border-emerald-400/40 text-emerald-700 dark:text-emerald-300 text-xs font-extrabold"
                >
                  <ShieldCheck className="w-3.5 h-3.5" />
                  <span>Terverifikasi</span>
                </span>

                <span
                  data-testid="profile-tier-badge"
                  className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-400/20 border border-amber-300/40 text-amber-800 dark:text-amber-200 text-xs font-extrabold"
                >
                  <Sparkles className="w-3.5 h-3.5" />
                  <span>{activeMember.role}</span>
                </span>

                <span className="glacier-pill text-xs font-mono-tabular text-slate-700 dark:text-sky-200 px-2.5 py-1 rounded-full">
                  ID: {activeMember.memberCode}
                </span>
              </div>

              {/* Member Full Name */}
              <h1
                data-testid="profile-member-name"
                className="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white"
              >
                {activeMember.name}
              </h1>

              {/* Validity Period Info */}
              <div className="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-700 dark:text-sky-100/90 font-mono-tabular">
                <span className="inline-flex items-center gap-1.5">
                  <Calendar className="w-3.5 h-3.5 text-amber-500 dark:text-amber-300" />
                  <span>Bergabung: {joinDateDisplay}</span>
                </span>
                <span aria-hidden="true">·</span>
                <span className="inline-flex items-center gap-1.5 text-emerald-600 dark:text-emerald-300 font-semibold">
                  <span>Kedaluwarsa: {expiryDateDisplay}</span>
                </span>
              </div>
            </div>
          </div>

          {/* Right Action Column: Prominent "Buka Kartu Anggota Digital" Button */}
          <div className="flex flex-col sm:flex-row lg:flex-col items-stretch sm:items-center lg:items-end gap-3 shrink-0">
            <button
              type="button"
              onClick={onOpenDigitalCard}
              data-testid="open-digital-card-btn"
              className="px-5 py-3.5 rounded-2xl bg-gradient-to-r from-[#FF6B35] to-[#F97316] hover:from-[#e85a25] hover:to-[#ea580c] text-white text-xs sm:text-sm font-extrabold shadow-xl border border-white/25 flex items-center justify-center gap-2.5 transition-transform active:scale-95 cursor-pointer whitespace-nowrap"
            >
              <QrCode className="w-4 h-4" />
              <span>Buka Kartu Anggota Digital</span>
            </button>
          </div>
        </div>
      </section>

      {/* B. BENTO RINGKASAN METRIK ANGGOTA (Transparent Glacier Glass Tiles) */}
      <section aria-label="Bento Ringkasan Metrik Anggota" className="grid grid-cols-1 md:grid-cols-3 gap-4">
        {/* 1. Kartu Transaksi Aktif */}
        <button
          type="button"
          onClick={() => setOpenPanel('borrowed')}
          data-testid="bento-active-transactions"
          className="glacier-tile rounded-3xl p-5 text-left transition-all cursor-pointer flex flex-col justify-between"
        >
          <div className="flex items-center justify-between w-full">
            <span className="text-xs font-extrabold uppercase tracking-wider text-sky-700 dark:text-sky-300">
              Transaksi Aktif
            </span>
            <div className="w-9 h-9 rounded-2xl bg-sky-500/20 border border-sky-400/35 flex items-center justify-center text-sky-600 dark:text-sky-300">
              <BookOpen className="w-4 h-4" />
            </div>
          </div>
          <div className="my-3">
            <p className="text-xl sm:text-2xl font-extrabold font-mono-tabular text-slate-900 dark:text-white">
              {totalTransactionsCount} Transaksi / {activeBorrowedCount} Sedang Dipinjam
            </p>
          </div>
          <p className="text-xs text-slate-700 dark:text-slate-300 flex items-center justify-between">
            <span>Kuota Sisa: {Math.max(0, activeMember.maxQuota - activeBorrowedCount)} dari {activeMember.maxQuota} Buku</span>
            <span className="font-bold text-sky-600 dark:text-sky-300">Lihat Buku →</span>
          </p>
        </button>

        {/* 2. Kartu Status Pelanggaran */}
        <button
          type="button"
          onClick={() => onNavigateTab('transactions')}
          data-testid="bento-violation-status"
          className="glacier-tile rounded-3xl p-5 text-left transition-all cursor-pointer flex flex-col justify-between"
        >
          <div className="flex items-center justify-between w-full">
            <span className="text-xs font-extrabold uppercase tracking-wider text-emerald-700 dark:text-emerald-300">
              Status Pelanggaran
            </span>
            <div className="w-9 h-9 rounded-2xl bg-emerald-500/20 border border-emerald-400/30 flex items-center justify-center text-emerald-600 dark:text-emerald-300">
              <CheckCircle2 className="w-4 h-4" />
            </div>
          </div>
          <div className="my-3">
            <span className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-2xl bg-emerald-500/20 border border-emerald-400/40 text-emerald-800 dark:text-emerald-200 text-sm sm:text-base font-extrabold">
              <ShieldCheck className="w-4 h-4 text-emerald-600 dark:text-emerald-300 shrink-0" />
              <span>
                {violationCount === 0
                  ? '0 Pelanggaran & Bebas Denda'
                  : `${violationCount} Catatan Keterlambatan`}
              </span>
            </span>
          </div>
          <p className="text-xs text-slate-700 dark:text-slate-300">
            Reputasi peminjaman sangat baik · Hak pinjam penuh aktif
          </p>
        </button>

        {/* 3. Kartu Buku Favorit */}
        <button
          type="button"
          onClick={() => onNavigateTab('overview')}
          data-testid="bento-favorite-books"
          className="glacier-tile rounded-3xl p-5 text-left transition-all cursor-pointer flex flex-col justify-between"
        >
          <div className="flex items-center justify-between w-full">
            <span className="text-xs font-extrabold uppercase tracking-wider text-amber-700 dark:text-amber-300">
              Buku Favorit
            </span>
            <div className="w-9 h-9 rounded-2xl bg-amber-500/20 border border-amber-400/30 flex items-center justify-center text-amber-600 dark:text-amber-300">
              <Bookmark className="w-4 h-4" />
            </div>
          </div>
          <div className="my-3">
            <p className="text-2xl sm:text-3xl font-extrabold font-mono-tabular text-slate-900 dark:text-white">
              {bookmarkedIds.length} Koleksi Tersimpan
            </p>
          </div>
          <p className="text-xs text-slate-700 dark:text-slate-300 flex items-center justify-between">
            <span>Tersinkronisasi dengan daftar bacaan</span>
            <span className="font-bold text-amber-600 dark:text-amber-300">Buka Favorit →</span>
          </p>
        </button>
      </section>

      {/* C & D. TWO-COLUMN OR STACKED GROUPS: PENGATURAN AKUN & RIWAYAT AKTIVITAS */}
      <div className="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        {/* C. KELOMPOK MENU PENGATURAN AKUN */}
        <section
          aria-label="Kelompok Menu Pengaturan Akun"
          className="glacier-card lg:col-span-6 rounded-3xl p-5 sm:p-6 space-y-3"
        >
          <div className="pb-2 border-b border-sky-200/40 dark:border-sky-300/15">
            <h2 className="text-base sm:text-lg font-extrabold text-slate-900 dark:text-white">
              Kelompok Menu Pengaturan Akun
            </h2>
            <p className="text-xs text-slate-700 dark:text-slate-300">
              Klik menu di bawah untuk mengelola biodata, pinjaman aktif, kontak, dan keamanan
            </p>
          </div>

          {/* C1. Biodata Diri */}
          <div className="glacier-tile rounded-2xl overflow-hidden">
            <button
              type="button"
              onClick={() => toggleSection('biodata')}
              data-testid="menu-biodata-btn"
              className="w-full p-4 flex items-center justify-between gap-3 text-left hover:bg-white/40 dark:hover:bg-white/[0.05] transition-colors cursor-pointer"
            >
              <div className="flex items-center gap-3">
                <div className="w-10 h-10 rounded-xl bg-indigo-500/20 border border-indigo-400/30 flex items-center justify-center text-indigo-600 dark:text-indigo-300 shrink-0">
                  <UserCheck className="w-5 h-5" />
                </div>
                <div>
                  <p className="text-sm font-bold text-slate-900 dark:text-white">Biodata Diri</p>
                  <p className="text-xs text-slate-700 dark:text-slate-300">
                    NPP ({nppInput}), NIK/ID, instansi & alamat domisili
                  </p>
                </div>
              </div>
              {openPanel === 'biodata' ? (
                <ChevronDown className="w-4 h-4 text-slate-700" />
              ) : (
                <ChevronRight className="w-4 h-4 text-slate-700" />
              )}
            </button>

            {openPanel === 'biodata' && (
              <form
                onSubmit={handleSaveBiodata}
                className="px-4 pb-4 pt-2 border-t border-slate-200/70 dark:border-white/10 space-y-3 text-xs"
              >
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                  <div>
                    <label className="block font-bold text-slate-700 dark:text-slate-300 mb-1">
                      Nomor Pokok Perpustakaan (NPP)
                    </label>
                    <input
                      type="text"
                      value={nppInput}
                      onChange={(e) => setNppInput(e.target.value)}
                      className="w-full px-3 py-2 rounded-xl bg-white/80 dark:bg-slate-900/80 border border-slate-300 dark:border-white/15 font-mono-tabular text-slate-900 dark:text-white"
                    />
                  </div>
                  <div>
                    <label className="block font-bold text-slate-700 dark:text-slate-300 mb-1">
                      NIK / Nomor Identitas Nasional
                    </label>
                    <input
                      type="text"
                      value={nikInput}
                      onChange={(e) => setNikInput(e.target.value)}
                      className="w-full px-3 py-2 rounded-xl bg-white/80 dark:bg-slate-900/80 border border-slate-300 dark:border-white/15 font-mono-tabular text-slate-900 dark:text-white"
                    />
                  </div>
                </div>
                <div>
                  <label className="block font-bold text-slate-700 dark:text-slate-300 mb-1">
                    Instansi / Fakultas
                  </label>
                  <input
                    type="text"
                    value={departmentInput}
                    onChange={(e) => setDepartmentInput(e.target.value)}
                    className="w-full px-3 py-2 rounded-xl bg-white/80 dark:bg-slate-900/80 border border-slate-300 dark:border-white/15 text-slate-900 dark:text-white"
                  />
                </div>
                <div>
                  <label className="block font-bold text-slate-700 dark:text-slate-300 mb-1">
                    Alamat Domisili
                  </label>
                  <input
                    type="text"
                    value={addressInput}
                    onChange={(e) => setAddressInput(e.target.value)}
                    className="w-full px-3 py-2 rounded-xl bg-white/80 dark:bg-slate-900/80 border border-slate-300 dark:border-white/15 text-slate-900 dark:text-white"
                  />
                </div>
                <div className="flex justify-end">
                  <button
                    type="submit"
                    className="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold cursor-pointer"
                  >
                    Simpan Biodata
                  </button>
                </div>
              </form>
            )}
          </div>

          {/* C2. Buku Sedang Dipinjam */}
          <div className="glacier-tile rounded-2xl overflow-hidden">
            <button
              type="button"
              onClick={() => toggleSection('borrowed')}
              data-testid="menu-borrowed-books-btn"
              className="w-full p-4 flex items-center justify-between gap-3 text-left hover:bg-white/40 dark:hover:bg-white/[0.05] transition-colors cursor-pointer"
            >
              <div className="flex items-center gap-3">
                <div className="w-10 h-10 rounded-xl bg-amber-500/20 border border-amber-400/30 flex items-center justify-center text-amber-600 dark:text-amber-300 shrink-0">
                  <Clock className="w-5 h-5" />
                </div>
                <div>
                  <p className="text-sm font-bold text-slate-900 dark:text-white">
                    Buku Sedang Dipinjam ({memberActiveLoans.length} Buku)
                  </p>
                  <p className="text-xs text-slate-700 dark:text-slate-300">
                    Daftar buku yang dibawa pulang & indikator sisa hari tenggat pengembalian
                  </p>
                </div>
              </div>
              {openPanel === 'borrowed' ? (
                <ChevronDown className="w-4 h-4 text-slate-700" />
              ) : (
                <ChevronRight className="w-4 h-4 text-slate-700" />
              )}
            </button>

            {openPanel === 'borrowed' && (
              <div className="px-4 pb-4 pt-2 border-t border-slate-200/70 dark:border-white/10 space-y-2.5">
                {memberActiveLoans.map((loan) => {
                  const bookItem = books.find((b) => b.id === loan.bookId) || books[0];
                  const daysRemaining = calculateDaysRemaining(loan.dueDate, referenceDate);
                  const prog = calculateReadingProgress(loan.currentPage, loan.totalPages);
                  return (
                    <div
                      key={loan.id}
                      className="glacier-pill p-3 rounded-2xl flex items-center justify-between gap-3"
                    >
                      <div className="flex items-center gap-3 min-w-0 flex-1">
                        <BookCoverArt book={bookItem} size="xs" />
                        <div className="min-w-0 flex-1">
                          <button
                            type="button"
                            onClick={() => onOpenBookDetail(bookItem.id)}
                            className="w-full text-xs font-bold text-slate-900 dark:text-white hover:underline truncate block text-left cursor-pointer"
                          >
                            {loan.bookTitle}
                          </button>
                          <p className="text-[11px] text-slate-700 dark:text-sky-200/80 font-mono-tabular truncate">
                            Tenggat: {loan.dueDate} · Progres {prog.percentage}%
                          </p>
                        </div>
                      </div>

                      <span
                        className={`px-2.5 py-1 rounded-full text-[11px] font-mono-tabular font-extrabold shrink-0 ${
                          daysRemaining >= 0
                            ? 'bg-emerald-500/20 border border-emerald-400/40 text-emerald-700 dark:text-emerald-300'
                            : 'bg-rose-500/20 border border-rose-400/40 text-rose-600 dark:text-rose-300'
                        }`}
                      >
                        {daysRemaining >= 0
                          ? `Sisa ${daysRemaining} hari`
                          : `Terlambat ${Math.abs(daysRemaining)} hari`}
                      </span>
                    </div>
                  );
                })}
              </div>
            )}
          </div>

          {/* C3. Pengaturan Kontak */}
          <div className="glacier-tile rounded-2xl overflow-hidden">
            <button
              type="button"
              onClick={() => toggleSection('contact')}
              data-testid="menu-contact-btn"
              className="w-full p-4 flex items-center justify-between gap-3 text-left hover:bg-white/40 dark:hover:bg-white/[0.05] transition-colors cursor-pointer"
            >
              <div className="flex items-center gap-3">
                <div className="w-10 h-10 rounded-xl bg-emerald-500/20 border border-emerald-400/30 flex items-center justify-center text-emerald-600 dark:text-emerald-300 shrink-0">
                  <Mail className="w-5 h-5" />
                </div>
                <div>
                  <p className="text-sm font-bold text-slate-900 dark:text-white">
                    Pengaturan Kontak
                  </p>
                  <p className="text-xs text-slate-700 dark:text-slate-300">
                    Email ({emailInput}) & No. WhatsApp ({phoneInput}) Terverifikasi
                  </p>
                </div>
              </div>
              {openPanel === 'contact' ? (
                <ChevronDown className="w-4 h-4 text-slate-700" />
              ) : (
                <ChevronRight className="w-4 h-4 text-slate-700" />
              )}
            </button>

            {openPanel === 'contact' && (
              <form
                onSubmit={handleSaveContact}
                className="px-4 pb-4 pt-2 border-t border-slate-200/70 dark:border-white/10 space-y-3 text-xs"
              >
                <div>
                  <div className="flex items-center justify-between mb-1">
                    <label className="font-bold text-slate-700 dark:text-slate-300">
                      Alamat Email Akademik / Pribadi
                    </label>
                    <span className="text-[10px] font-bold text-emerald-600 dark:text-emerald-300">
                      ✓ Terverifikasi
                    </span>
                  </div>
                  <input
                    type="email"
                    value={emailInput}
                    onChange={(e) => setEmailInput(e.target.value)}
                    className="w-full px-3 py-2 rounded-xl bg-white/80 dark:bg-slate-900/80 border border-slate-300 dark:border-white/15 text-slate-900 dark:text-white"
                  />
                </div>
                <div>
                  <div className="flex items-center justify-between mb-1">
                    <label className="font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1">
                      <Phone className="w-3 h-3" /> Nomor WhatsApp Notifikasi Sirkulasi
                    </label>
                    <span className="text-[10px] font-bold text-emerald-600 dark:text-emerald-300">
                      ✓ Terverifikasi
                    </span>
                  </div>
                  <input
                    type="text"
                    value={phoneInput}
                    onChange={(e) => setPhoneInput(e.target.value)}
                    className="w-full px-3 py-2 rounded-xl bg-white/80 dark:bg-slate-900/80 border border-slate-300 dark:border-white/15 font-mono-tabular text-slate-900 dark:text-white"
                  />
                </div>
                <div className="flex justify-end">
                  <button
                    type="submit"
                    className="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold cursor-pointer"
                  >
                    Simpan Kontak
                  </button>
                </div>
              </form>
            )}
          </div>

          {/* C4. Keamanan Akun & Ganti Kata Sandi */}
          <div className="glacier-tile rounded-2xl overflow-hidden">
            <button
              type="button"
              onClick={() => toggleSection('security')}
              data-testid="menu-security-btn"
              className="w-full p-4 flex items-center justify-between gap-3 text-left hover:bg-white/40 dark:hover:bg-white/[0.05] transition-colors cursor-pointer"
            >
              <div className="flex items-center gap-3">
                <div className="w-10 h-10 rounded-xl bg-violet-500/20 border border-violet-400/30 flex items-center justify-center text-violet-600 dark:text-violet-300 shrink-0">
                  <Lock className="w-5 h-5" />
                </div>
                <div>
                  <p className="text-sm font-bold text-slate-900 dark:text-white">
                    Keamanan Akun & Ganti Kata Sandi
                  </p>
                  <p className="text-xs text-slate-700 dark:text-slate-300">
                    Pembaruan sandi, PIN kios mandiri & autentikasi 2 langkah
                  </p>
                </div>
              </div>
              {openPanel === 'security' ? (
                <ChevronDown className="w-4 h-4 text-slate-700" />
              ) : (
                <ChevronRight className="w-4 h-4 text-slate-700" />
              )}
            </button>

            {openPanel === 'security' && (
              <form
                onSubmit={handleSaveSecurity}
                className="px-4 pb-4 pt-2 border-t border-slate-200/70 dark:border-white/10 space-y-3 text-xs"
              >
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                  <div>
                    <label className="block font-bold text-slate-700 dark:text-slate-300 mb-1">
                      Kata Sandi Saat Ini
                    </label>
                    <input
                      type="password"
                      value={oldPassword}
                      onChange={(e) => setOldPassword(e.target.value)}
                      placeholder="••••••••"
                      className="w-full px-3 py-2 rounded-xl bg-white/80 dark:bg-slate-900/80 border border-slate-300 dark:border-white/15 text-slate-900 dark:text-white"
                    />
                  </div>
                  <div>
                    <label className="block font-bold text-slate-700 dark:text-slate-300 mb-1">
                      Kata Sandi Baru
                    </label>
                    <input
                      type="password"
                      value={newPassword}
                      onChange={(e) => setNewPassword(e.target.value)}
                      placeholder="Minimal 8 karakter"
                      className="w-full px-3 py-2 rounded-xl bg-white/80 dark:bg-slate-900/80 border border-slate-300 dark:border-white/15 text-slate-900 dark:text-white"
                    />
                  </div>
                </div>
                <div className="flex items-center justify-between pt-1">
                  <label className="inline-flex items-center gap-2 font-semibold text-slate-700 dark:text-slate-200 cursor-pointer">
                    <input
                      type="checkbox"
                      checked={twoFactorEnabled}
                      onChange={(e) => setTwoFactorEnabled(e.target.checked)}
                      className="rounded border-slate-300"
                    />
                    <span>Aktifkan Verifikasi 2 Langkah (OTP WhatsApp)</span>
                  </label>
                  <button
                    type="submit"
                    className="px-4 py-2 rounded-xl bg-violet-600 hover:bg-violet-500 text-white font-bold cursor-pointer"
                  >
                    Perbarui Sandi
                  </button>
                </div>
              </form>
            )}
          </div>
        </section>

        {/* D. KELOMPOK MENU RIWAYAT AKTIVITAS */}
        <section
          aria-label="Kelompok Menu Riwayat Aktivitas"
          className="glacier-card lg:col-span-6 rounded-3xl p-5 sm:p-6 space-y-3"
        >
          <div className="pb-2 border-b border-sky-200/40 dark:border-sky-300/15">
            <h2 className="text-base sm:text-lg font-extrabold text-slate-900 dark:text-white">
              Kelompok Menu Riwayat Aktivitas
            </h2>
            <p className="text-xs text-slate-700 dark:text-slate-300">
              Riwayat sirkulasi selesai, reservasi kursi baca cabang, serta usulan & donasi buku
            </p>
          </div>

          {/* D1. Riwayat Transaksi & Sirkulasi (Status Selesai / Dikembalikan + Tombol Tulis Ulasan) */}
          <div className="glacier-tile rounded-2xl overflow-hidden">
            <button
              type="button"
              onClick={() => toggleSection('history-circulation')}
              data-testid="menu-history-circulation-btn"
              className="w-full p-4 flex items-center justify-between gap-3 text-left hover:bg-white/40 dark:hover:bg-white/[0.05] transition-colors cursor-pointer"
            >
              <div className="flex items-center gap-3">
                <div className="w-10 h-10 rounded-xl bg-sky-500/20 border border-sky-400/30 flex items-center justify-center text-sky-600 dark:text-sky-300 shrink-0">
                  <History className="w-5 h-5" />
                </div>
                <div>
                  <p className="text-sm font-bold text-slate-900 dark:text-white">
                    Riwayat Transaksi & Sirkulasi ({memberReturnedLoans.length} Selesai)
                  </p>
                  <p className="text-xs text-slate-700 dark:text-slate-300">
                    Koleksi dengan status Selesai / Dikembalikan & fitur Tulis Ulasan
                  </p>
                </div>
              </div>
              {openPanel === 'history-circulation' ? (
                <ChevronDown className="w-4 h-4 text-slate-700" />
              ) : (
                <ChevronRight className="w-4 h-4 text-slate-700" />
              )}
            </button>

            {openPanel === 'history-circulation' && (
              <div className="px-4 pb-4 pt-2 border-t border-slate-200/70 dark:border-white/10 space-y-3">
                {memberReturnedLoans.map((loan) => {
                  const bookItem = books.find((b) => b.id === loan.bookId) || books[0];
                  const savedReview = submittedLoanReviews[loan.id];
                  const isReviewing = reviewingLoanId === loan.id;

                  return (
                    <div
                      key={loan.id}
                      className="p-3.5 rounded-2xl bg-white/65 dark:bg-slate-900/75 border border-slate-200/80 dark:border-white/10 space-y-2.5"
                    >
                      <div className="flex items-center justify-between gap-3">
                        <div className="flex items-center gap-3 min-w-0">
                          <BookCoverArt book={bookItem} size="xs" />
                          <div className="min-w-0">
                            <p className="text-xs font-bold text-slate-900 dark:text-white truncate">
                              {loan.bookTitle}
                            </p>
                            <div className="flex flex-wrap items-center gap-1.5 mt-0.5">
                              <span className="px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 text-[10px] font-bold">
                                Selesai / Dikembalikan
                              </span>
                              <span className="text-[10px] font-mono-tabular text-slate-700 dark:text-slate-400">
                                {loan.returnDate || '22 Okt 2026'}
                              </span>
                            </div>
                          </div>
                        </div>

                        <button
                          type="button"
                          onClick={() =>
                            setReviewingLoanId(isReviewing ? null : loan.id)
                          }
                          data-testid={`write-review-btn-${loan.id}`}
                          className="px-3 py-1.5 rounded-xl bg-amber-400/20 hover:bg-amber-400/35 border border-amber-400/40 text-amber-800 dark:text-amber-200 text-xs font-bold flex items-center gap-1.5 shrink-0 cursor-pointer"
                        >
                          <Star className="w-3.5 h-3.5 fill-current" />
                          <span>{savedReview ? 'Edit Ulasan' : 'Tulis Ulasan'}</span>
                        </button>
                      </div>

                      {savedReview && !isReviewing && (
                        <p className="text-[11px] italic text-emerald-700 dark:text-emerald-300 bg-emerald-500/10 px-3 py-1.5 rounded-xl">
                          Ulasan Anda: {savedReview}
                        </p>
                      )}

                      {isReviewing && (
                        <div className="pt-2 border-t border-slate-200 dark:border-white/10 space-y-2">
                          <div className="flex items-center justify-between text-xs">
                            <span className="font-bold text-slate-700 dark:text-slate-300">
                              Beri Rating Bintang:
                            </span>
                            <div className="flex items-center gap-1">
                              {[1, 2, 3, 4, 5].map((star) => (
                                <button
                                  key={star}
                                  type="button"
                                  onClick={() => setReviewRating(star)}
                                  className={`p-1 cursor-pointer ${
                                    star <= reviewRating ? 'text-amber-400' : 'text-slate-400'
                                  }`}
                                >
                                  <Star className="w-4 h-4 fill-current" />
                                </button>
                              ))}
                            </div>
                          </div>
                          <div className="flex items-center gap-2">
                            <input
                              type="text"
                              value={reviewComment}
                              onChange={(e) => setReviewComment(e.target.value)}
                              placeholder={`Bagikan kesan membaca ${loan.bookTitle}...`}
                              className="flex-1 px-3 py-1.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-300 dark:border-white/15 text-xs text-slate-900 dark:text-white"
                            />
                            <button
                              type="button"
                              onClick={() => handlePublishReview(loan)}
                              className="px-3 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold flex items-center gap-1 cursor-pointer"
                            >
                              <Send className="w-3 h-3" />
                              <span>Kirim</span>
                            </button>
                          </div>
                        </div>
                      )}
                    </div>
                  );
                })}
              </div>
            )}
          </div>

          {/* D2. Riwayat Reservasi Kunjungan Cabang */}
          <div className="glacier-tile rounded-2xl overflow-hidden">
            <button
              type="button"
              onClick={() => toggleSection('history-reservation')}
              data-testid="menu-history-reservation-btn"
              className="w-full p-4 flex items-center justify-between gap-3 text-left hover:bg-white/40 dark:hover:bg-white/[0.05] transition-colors cursor-pointer"
            >
              <div className="flex items-center gap-3">
                <div className="w-10 h-10 rounded-xl bg-rose-500/20 border border-rose-400/30 flex items-center justify-center text-rose-600 dark:text-rose-300 shrink-0">
                  <MapPin className="w-5 h-5" />
                </div>
                <div>
                  <p className="text-sm font-bold text-slate-900 dark:text-white">
                    Riwayat Reservasi Kunjungan Cabang ({reservations.length})
                  </p>
                  <p className="text-xs text-slate-700 dark:text-slate-300">
                    Tiket kursi baca hening, ruang diskusi & loker cabang fisik
                  </p>
                </div>
              </div>
              {openPanel === 'history-reservation' ? (
                <ChevronDown className="w-4 h-4 text-slate-700" />
              ) : (
                <ChevronRight className="w-4 h-4 text-slate-700" />
              )}
            </button>

            {openPanel === 'history-reservation' && (
              <div className="px-4 pb-4 pt-2 border-t border-slate-200/70 dark:border-white/10 space-y-2.5">
                {reservations.map((res) => (
                  <div
                    key={res.id}
                    className="p-3 rounded-2xl bg-white/65 dark:bg-slate-900/75 border border-slate-200/80 dark:border-white/10 flex items-center justify-between gap-3 text-xs"
                  >
                    <div>
                      <p className="font-bold text-slate-900 dark:text-white">{res.branchName}</p>
                      <p className="text-[11px] text-slate-700 dark:text-slate-300">{res.seatZone}</p>
                      <p className="text-[10px] font-mono-tabular text-slate-700 dark:text-slate-400 mt-0.5">
                        {res.code} · {res.visitDate} ({res.timeSlot})
                      </p>
                    </div>
                    <span
                      className={`px-2.5 py-1 rounded-full text-[10px] font-bold shrink-0 ${
                        res.status === 'Terjadwal'
                          ? 'bg-indigo-500/20 text-indigo-700 dark:text-indigo-300'
                          : 'bg-emerald-500/20 text-emerald-700 dark:text-emerald-300'
                      }`}
                    >
                      {res.status}
                    </span>
                  </div>
                ))}

                <button
                  type="button"
                  onClick={handleAddQuickReservation}
                  className="w-full py-2 px-3 rounded-xl bg-indigo-500/15 hover:bg-indigo-500/25 border border-indigo-400/30 text-indigo-700 dark:text-indigo-200 text-xs font-bold flex items-center justify-center gap-1.5 cursor-pointer"
                >
                  <Plus className="w-3.5 h-3.5" />
                  <span>Buat Reservasi Kunjungan Cabang Baru</span>
                </button>
              </div>
            )}
          </div>

          {/* D3. Riwayat Usulan Koleksi Buku & Donasi Buku */}
          <div className="glacier-tile rounded-2xl overflow-hidden">
            <button
              type="button"
              onClick={() => toggleSection('history-donation')}
              data-testid="menu-history-donation-btn"
              className="w-full p-4 flex items-center justify-between gap-3 text-left hover:bg-white/40 dark:hover:bg-white/[0.05] transition-colors cursor-pointer"
            >
              <div className="flex items-center gap-3">
                <div className="w-10 h-10 rounded-xl bg-purple-500/20 border border-purple-400/30 flex items-center justify-center text-purple-600 dark:text-purple-300 shrink-0">
                  <Gift className="w-5 h-5" />
                </div>
                <div>
                  <p className="text-sm font-bold text-slate-900 dark:text-white">
                    Riwayat Usulan Koleksi Buku & Donasi Buku ({proposals.length})
                  </p>
                  <p className="text-xs text-slate-700 dark:text-slate-300">
                    Status pengadaan buku usulan anggota & hibah literasi
                  </p>
                </div>
              </div>
              {openPanel === 'history-donation' ? (
                <ChevronDown className="w-4 h-4 text-slate-700" />
              ) : (
                <ChevronRight className="w-4 h-4 text-slate-700" />
              )}
            </button>

            {openPanel === 'history-donation' && (
              <div className="px-4 pb-4 pt-2 border-t border-slate-200/70 dark:border-white/10 space-y-3 text-xs">
                <div className="space-y-2">
                  {proposals.map((item) => (
                    <div
                      key={item.id}
                      className="p-3 rounded-2xl bg-white/65 dark:bg-slate-900/75 border border-slate-200/80 dark:border-white/10 flex items-center justify-between gap-2"
                    >
                      <div className="min-w-0">
                        <span className="text-[10px] font-extrabold uppercase text-purple-600 dark:text-purple-300">
                          {item.type} · {item.submittedDate}
                        </span>
                        <p className="font-bold text-slate-900 dark:text-white truncate">
                          {item.title}
                        </p>
                        <p className="text-[11px] text-slate-700 dark:text-slate-400">
                          Penulis: {item.author}
                        </p>
                      </div>
                      <span className="px-2.5 py-1 rounded-full bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 text-[10px] font-bold shrink-0">
                        {item.status}
                      </span>
                    </div>
                  ))}
                </div>

                <form onSubmit={handleAddProposal} className="pt-2 border-t border-slate-200 dark:border-white/10 space-y-2">
                  <div className="flex items-center gap-2">
                    <select
                      value={newProposalType}
                      onChange={(e) =>
                        setNewProposalType(e.target.value as 'Usulan Koleksi' | 'Donasi Buku')
                      }
                      className="px-2.5 py-1.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-300 dark:border-white/15 text-xs text-slate-900 dark:text-white"
                    >
                      <option value="Usulan Koleksi">Usulan Koleksi</option>
                      <option value="Donasi Buku">Donasi Buku</option>
                    </select>
                    <input
                      type="text"
                      value={newProposalTitle}
                      onChange={(e) => setNewProposalTitle(e.target.value)}
                      placeholder="Judul buku..."
                      className="flex-1 px-3 py-1.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-300 dark:border-white/15 text-xs text-slate-900 dark:text-white"
                    />
                  </div>
                  <div className="flex items-center gap-2">
                    <input
                      type="text"
                      value={newProposalAuthor}
                      onChange={(e) => setNewProposalAuthor(e.target.value)}
                      placeholder="Nama penulis..."
                      className="flex-1 px-3 py-1.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-300 dark:border-white/15 text-xs text-slate-900 dark:text-white"
                    />
                    <button
                      type="submit"
                      className="px-3.5 py-1.5 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-bold cursor-pointer shrink-0"
                    >
                      + Kirim
                    </button>
                  </div>
                </form>
              </div>
            )}
          </div>
        </section>
      </div>

      {/* E. FOOTER HALAMAN PROFIL ANGGOTA */}
      <section
        aria-label="Footer Halaman Profil Anggota"
        className="glacier-card rounded-3xl p-5 sm:p-6 space-y-4"
      >
        <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
          {/* Menu Bantuan, Kebijakan Privasi, Ganti Bahasa */}
          <div className="flex flex-wrap items-center gap-2.5">
            <button
              type="button"
              onClick={onOpenSupportModal}
              className="glacier-pill inline-flex items-center gap-2 px-3.5 py-2 rounded-2xl text-xs font-bold text-slate-800 dark:text-slate-100 transition-colors cursor-pointer"
            >
              <HelpCircle className="w-4 h-4 text-indigo-500 dark:text-indigo-300" />
              <span>Bantuan Pustakawan</span>
            </button>

            <button
              type="button"
              onClick={() => toggleSection('privacy')}
              className="glacier-pill inline-flex items-center gap-2 px-3.5 py-2 rounded-2xl text-xs font-bold text-slate-800 dark:text-slate-100 transition-colors cursor-pointer"
            >
              <FileText className="w-4 h-4 text-emerald-500 dark:text-emerald-300" />
              <span>Kebijakan Privasi</span>
            </button>

            <button
              type="button"
              onClick={() => {
                const nextLang = appLanguage === 'ID' ? 'EN' : 'ID';
                setAppLanguage(nextLang);
                triggerFeedback(
                  nextLang === 'ID'
                    ? 'Bahasa antarmuka disetel ke Bahasa Indonesia (ID).'
                    : 'Interface language switched to Bahasa Indonesia / English (ID/EN).'
                );
              }}
              className="glacier-pill inline-flex items-center gap-2 px-3.5 py-2 rounded-2xl text-xs font-bold text-slate-800 dark:text-slate-100 transition-colors cursor-pointer"
            >
              <Globe className="w-4 h-4 text-amber-500 dark:text-amber-300" />
              <span>Ganti Bahasa: {appLanguage === 'ID' ? 'Indonesia (ID)' : 'English (EN)'}</span>
            </button>
          </div>

          {/* Info Versi Aplikasi */}
          <div className="flex items-center justify-between md:justify-end gap-4">
            <span
              data-testid="app-version-label"
              className="text-xs font-mono-tabular font-bold text-slate-700 dark:text-slate-300"
            >
              BiblioVerse v1.0.0
            </span>
          </div>
        </div>

        {openPanel === 'privacy' && (
          <div className="p-4 rounded-2xl bg-white/50 dark:bg-slate-900/70 border border-slate-200 dark:border-white/10 text-xs text-slate-700 dark:text-slate-200 leading-relaxed">
            <p className="font-bold text-slate-900 dark:text-white mb-1">
              Kebijakan Privasi & Perlindungan Data Anggota (UU PDP & Standar Perpustakaan Nasional)
            </p>
            <p>
              Nomor Induk Kependudukan (NIK/ID) dan Nomor Pokok Perpustakaan (NPP) Anda dienkripsi
              serta disensor secara otomatis pada layar publik. Data riwayat bacaan dan sirkulasi
              hanya digunakan untuk keperluan layanan peminjaman koleksi fisik maupun E-Book.
            </p>
          </div>
        )}
      </section>
    </div>
  );
};
