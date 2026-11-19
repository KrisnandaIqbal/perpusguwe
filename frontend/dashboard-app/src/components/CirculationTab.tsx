import React, { useState } from 'react';
import {
  RotateCcw,
  CalendarPlus,
  CheckCircle2,
  AlertTriangle,
  Clock,
  BookCheck,
  Search,
} from 'lucide-react';
import {
  type CirculationRecord,
  type Member,
  calculateReadingProgress,
  calculateDaysRemaining,
  formatIdr,
} from '../utils/libraryLogic';

interface CirculationTabProps {
  circulation: CirculationRecord[];
  activeMember: Member;
  referenceDate: string;
  onExtendLoan: (loanId: string) => void;
  onReturnBook: (loanId: string) => void;
  onUpdateReadingProgress: (loanId: string, additionalPages: number, additionalMinutes: number) => void;
}

export const CirculationTab: React.FC<CirculationTabProps> = ({
  circulation,
  activeMember,
  referenceDate,
  onExtendLoan,
  onReturnBook,
  onUpdateReadingProgress,
}) => {
  const [statusFilter, setStatusFilter] = useState<'Semua' | CirculationRecord['status']>('Semua');
  const [searchQuery, setSearchQuery] = useState('');

  const scopedLoans = circulation.filter((c) => c.memberId === activeMember.id);

  const filteredLoans = scopedLoans.filter((loan) => {
    if (statusFilter !== 'Semua' && loan.status !== statusFilter) return false;
    if (!searchQuery.trim()) return true;
    const q = searchQuery.toLowerCase();
    return (
      loan.bookTitle.toLowerCase().includes(q) ||
      loan.loanCode.toLowerCase().includes(q) ||
      loan.memberName.toLowerCase().includes(q) ||
      loan.ddcCode.toLowerCase().includes(q)
    );
  });

  const activeLoansCount = scopedLoans.filter(
    (c) => c.status === 'Dipinjam' || c.status === 'Diperpanjang'
  ).length;
  const overdueLoansCount = scopedLoans.filter((c) => c.status === 'Terlambat').length;
  const returnedLoansCount = scopedLoans.filter((c) => c.status === 'Dikembalikan').length;

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h1 className="text-2xl font-bold text-slate-900 dark:text-white">
            Layanan Sirkulasi Peminjaman & Pengembalian
          </h1>
          <p className="text-xs sm:text-sm text-slate-700 dark:text-slate-400">
            Pantau jatuh tempo buku, perpanjangan masa pinjam mandiri (+7 hari), dan pengembalian eksemplar
          </p>
        </div>

        <span className="px-3.5 py-1.5 rounded-xl glacier-pill text-sky-800 dark:text-sky-200 text-xs font-bold self-start">
          Anggota: {activeMember.name} ({activeMember.memberCode})
        </span>
      </div>

      {/* Bento Summary Row for Circulation (Transparent Glacier Glass Grid) */}
      <div className="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div className="relative overflow-hidden rounded-3xl glacier-tile p-5 flex flex-col justify-between">
          <div
            aria-hidden="true"
            className="pointer-events-none absolute -top-8 -right-8 w-24 h-24 rounded-full bg-amber-300/25 blur-2xl"
          />
          <span className="text-xs font-extrabold tracking-wide text-amber-700 dark:text-amber-300">
            SEDANG DIPINJAM
          </span>
          <p
            data-testid="circulation-active-count"
            className="text-4xl font-extrabold font-mono-tabular my-2 text-slate-900 dark:text-white"
          >
            {activeLoansCount}
          </p>
          <p className="text-xs font-semibold text-slate-700 dark:text-sky-100/80">
            Buku dalam masa baca aktif
          </p>
        </div>

        <div className="relative overflow-hidden rounded-3xl glacier-tile p-5 flex flex-col justify-between">
          <div
            aria-hidden="true"
            className="pointer-events-none absolute -top-8 -right-8 w-24 h-24 rounded-full bg-orange-400/25 blur-2xl"
          />
          <span className="text-xs font-extrabold tracking-wide text-orange-700 dark:text-orange-300">
            MELEWATI TENGGAT
          </span>
          <p className="text-4xl font-extrabold font-mono-tabular my-2 text-slate-900 dark:text-white">
            {overdueLoansCount}
          </p>
          <p className="text-xs font-semibold text-slate-700 dark:text-sky-100/80">
            Perlu dikembalikan segera
          </p>
        </div>

        <div className="relative overflow-hidden rounded-3xl glacier-tile p-5 flex flex-col justify-between">
          <div
            aria-hidden="true"
            className="pointer-events-none absolute -top-8 -right-8 w-24 h-24 rounded-full bg-emerald-400/25 blur-2xl"
          />
          <span className="text-xs font-extrabold tracking-wide text-emerald-700 dark:text-emerald-300">
            SELESAI KEMBALI
          </span>
          <p
            data-testid="circulation-returned-count"
            className="text-4xl font-extrabold font-mono-tabular my-2 text-slate-900 dark:text-white"
          >
            {returnedLoansCount}
          </p>
          <p className="text-xs font-semibold text-slate-700 dark:text-sky-100/80">
            Riwayat pengembalian tuntas
          </p>
        </div>

        <div className="relative overflow-hidden rounded-3xl glacier-tile p-5 flex flex-col justify-between">
          <div
            aria-hidden="true"
            className="pointer-events-none absolute -top-8 -right-8 w-24 h-24 rounded-full bg-sky-400/25 blur-2xl"
          />
          <span className="text-xs font-extrabold tracking-wide text-sky-700 dark:text-sky-300">
            SISA KUOTA PINJAM
          </span>
          <p className="text-4xl font-extrabold font-mono-tabular my-2 text-slate-900 dark:text-white">
            {Math.max(0, activeMember.maxQuota - activeMember.currentBorrowed)}
          </p>
          <p className="text-xs font-semibold text-slate-700 dark:text-sky-100/80">
            Dari batas {activeMember.maxQuota} buku ({activeMember.role})
          </p>
        </div>
      </div>

      {/* Circulation Filter & Ledger Table (Transparent Glacier Glass) */}
      <div className="rounded-3xl glacier-card p-5 space-y-4">
        <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
          <div className="relative flex-1 max-w-md">
            <Search className="w-4 h-4 text-sky-600 dark:text-sky-300 absolute left-3.5 top-1/2 -translate-y-1/2 z-10 pointer-events-none" />
            <input
              type="text"
              value={searchQuery}
              onChange={(e) => setSearchQuery(e.target.value)}
              placeholder="Cari kode sirkulasi (SRK), judul buku, atau nama peminjam..."
              aria-label="Cari data sirkulasi"
              className="w-full pl-10 pr-4 py-2 rounded-xl glacier-pill text-xs sm:text-sm text-slate-900 dark:text-white placeholder:text-slate-700 dark:placeholder:text-sky-100/65"
            />
          </div>

          <div className="flex flex-wrap items-center gap-1.5 p-1 rounded-xl glacier-pill">
            {(['Semua', 'Dipinjam', 'Diperpanjang', 'Terlambat', 'Dikembalikan'] as const).map(
              (st) => (
                <button
                  key={st}
                  type="button"
                  onClick={() => setStatusFilter(st)}
                  className={`px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors cursor-pointer whitespace-nowrap ${
                    statusFilter === st
                      ? 'bg-sky-500/85 text-white shadow-xs'
                      : 'text-slate-700 dark:text-sky-100/80 hover:text-slate-900 dark:hover:text-white'
                  }`}
                >
                  {st}
                </button>
              )
            )}
          </div>
        </div>

        <div className="overflow-x-auto">
          <table className="w-full text-left border-collapse">
            <thead>
              <tr className="border-b border-slate-200 text-[11px] font-bold text-slate-800">
                <th className="py-3 px-2">KODE, BUKU & PEMINJAM</th>
                <th className="py-3 px-2">PROGRES</th>
                <th className="py-3 px-2">TENGGAT & STATUS</th>
                <th className="py-3 px-2 text-right">TINDAKAN SIRKULASI</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-200/70 text-xs">
              {filteredLoans.map((loan) => {
                const prog = calculateReadingProgress(loan.currentPage, loan.totalPages);
                const daysLeft = calculateDaysRemaining(loan.dueDate, referenceDate);
                const isReturned = loan.status === 'Dikembalikan';

                return (
                  <tr
                    key={loan.id}
                    className="hover:bg-slate-50 transition-colors"
                  >
                    <td className="py-3 px-2 max-w-[205px]">
                      <div className="flex items-center gap-1.5 mb-0.5">
                        <span className="inline-block font-mono-tabular text-[10px] font-bold text-sky-700 bg-sky-50 border border-sky-200 px-1.5 py-0.5 rounded">
                          {loan.loanCode}
                        </span>
                        <span className="text-[11px] font-semibold text-slate-800 truncate">
                          {loan.memberName}
                        </span>
                      </div>
                      <p className="font-bold text-slate-900 truncate">
                        {loan.bookTitle}
                      </p>
                      <p className="text-[11px] text-slate-700 font-mono-tabular truncate">
                        {loan.bookAuthor} · {loan.ddcCode}
                      </p>
                    </td>

                    <td className="py-3 px-2 w-[95px]">
                      <div className="flex items-center justify-between text-[11px] font-mono-tabular mb-1">
                        <span className="font-bold text-slate-900">
                          {loan.currentPage}/{loan.totalPages}
                        </span>
                        <span className="text-slate-700 font-semibold">{prog.percentage}%</span>
                      </div>
                      <div className="h-1.5 w-full rounded-full bg-slate-200 overflow-hidden">
                        <div
                          className="h-full bg-sky-600 rounded-full"
                          style={{ width: `${prog.percentage}%` }}
                        />
                      </div>
                    </td>

                    <td className="py-3 px-2 whitespace-nowrap">
                      <div className="flex items-center gap-1.5">
                        <span
                          className={`inline-flex items-center gap-1 font-bold text-[11px] ${
                            loan.status === 'Dikembalikan'
                              ? 'text-emerald-700'
                              : loan.status === 'Terlambat'
                              ? 'text-rose-700'
                              : loan.status === 'Diperpanjang'
                              ? 'text-amber-700'
                              : 'text-sky-700'
                          }`}
                        >
                          {loan.status === 'Dikembalikan' ? (
                            <CheckCircle2 className="w-3.5 h-3.5" />
                          ) : loan.status === 'Terlambat' ? (
                            <AlertTriangle className="w-3.5 h-3.5" />
                          ) : (
                            <Clock className="w-3.5 h-3.5" />
                          )}
                          <span>{loan.status}</span>
                        </span>
                        <span className="font-mono-tabular font-bold text-slate-800">
                          · {loan.dueDate}
                        </span>
                      </div>
                      {isReturned ? (
                        <p className="text-[11px] font-semibold text-emerald-700 font-mono-tabular">
                          Kembali: {loan.returnDate}
                        </p>
                      ) : daysLeft >= 0 ? (
                        <p className="text-[11px] font-medium text-slate-700 font-mono-tabular">
                          Sisa {daysLeft}h ({loan.renewalCount}/{loan.maxRenewals}x)
                        </p>
                      ) : (
                        <p className="text-[11px] text-rose-700 font-bold font-mono-tabular">
                          Telat {Math.abs(daysLeft)}h · {formatIdr(loan.fineAmount)}
                        </p>
                      )}
                    </td>

                    <td className="py-3 px-2 text-right whitespace-nowrap">
                      {!isReturned ? (
                        <div className="inline-flex items-center gap-1">
                          <button
                            type="button"
                            onClick={() => onUpdateReadingProgress(loan.id, 25, 20)}
                            className="px-2 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-900 border border-slate-300 text-[11px] font-bold transition-colors cursor-pointer"
                            title="Tambah +25 halaman progres baca"
                          >
                            +25 Hal
                          </button>

                          {loan.status !== 'Terlambat' && loan.renewalCount < loan.maxRenewals && (
                            <button
                              type="button"
                              onClick={() => onExtendLoan(loan.id)}
                              aria-label={`Perpanjang pinjaman ${loan.bookTitle}`}
                              className="inline-flex items-center gap-1 px-2 py-1.5 rounded-lg bg-amber-100 hover:bg-amber-200 text-amber-900 border border-amber-300 text-[11px] font-bold transition-colors cursor-pointer"
                            >
                              <CalendarPlus className="w-3 h-3" />
                              <span>+7 Hari</span>
                            </button>
                          )}

                          <button
                            type="button"
                            onClick={() => onReturnBook(loan.id)}
                            aria-label={`Kembalikan buku ${loan.bookTitle}`}
                            className="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-[11px] font-bold shadow-xs transition-colors cursor-pointer"
                          >
                            <RotateCcw className="w-3 h-3" />
                            <span>Kembalikan</span>
                          </button>
                        </div>
                      ) : (
                        <span className="inline-flex items-center gap-1 text-[11px] font-bold text-slate-700">
                          <BookCheck className="w-3.5 h-3.5 text-emerald-600" />
                          <span>Selesai</span>
                        </span>
                      )}
                    </td>
                  </tr>
                );
              })}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  );
};
