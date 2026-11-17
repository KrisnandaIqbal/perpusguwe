import React, { useState } from 'react';
import {
  CheckCircle2,
  Receipt,
  Plus,
  Search,
  AlertCircle,
} from 'lucide-react';
import { type TransactionRecord, type Member, formatIdr } from '../utils/libraryLogic';

interface TransactionsTabProps {
  transactions: TransactionRecord[];
  activeMember: Member;
  onCreateTransaction: (
    newTrx: Omit<TransactionRecord, 'id' | 'invoiceCode' | 'date'>
  ) => void;
}

export const TransactionsTab: React.FC<TransactionsTabProps> = ({
  transactions,
  activeMember,
  onCreateTransaction,
}) => {
  const [searchQuery, setSearchQuery] = useState('');
  const [selectedReceipt, setSelectedReceipt] = useState<TransactionRecord | null>(null);
  const [isNewTrxModalOpen, setIsNewTrxModalOpen] = useState(false);

  // New transaction form state
  const [trxType, setTrxType] =
    useState<TransactionRecord['type']>('Perpanjangan Keanggotaan');
  const [trxDesc, setTrxDesc] = useState('');
  const [trxAmount, setTrxAmount] = useState('50000');
  const [formError, setFormError] = useState<string | null>(null);

  const filteredTransactions = transactions.filter((t) => {
    if (!searchQuery.trim()) return true;
    const q = searchQuery.toLowerCase();
    return (
      t.invoiceCode.toLowerCase().includes(q) ||
      t.memberName.toLowerCase().includes(q) ||
      t.description.toLowerCase().includes(q) ||
      t.type.toLowerCase().includes(q)
    );
  });

  const handleCreateNewTransaction = (e: React.FormEvent) => {
    e.preventDefault();
    const numericAmount = parseInt(trxAmount, 10);
    if (!trxDesc.trim() || isNaN(numericAmount) || numericAmount <= 0) {
      setFormError('Deskripsi transaksi dan nominal yang valid wajib diisi.');
      return;
    }

    onCreateTransaction({
      memberId: activeMember.id,
      memberName: activeMember.name,
      memberCode: activeMember.memberCode,
      type: trxType,
      description: trxDesc.trim(),
      amount: numericAmount,
      paymentMethod: 'QRIS Perpustakaan',
      status: 'Lunas',
    });

    setTrxDesc('');
    setTrxAmount('50000');
    setFormError(null);
    setIsNewTrxModalOpen(false);
  };

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h1 className="text-2xl font-bold text-slate-900 dark:text-white">
            Riwayat Transaksi & Kuitansi Perpustakaan
          </h1>
          <p className="text-xs sm:text-sm text-slate-700 dark:text-slate-400">
            Catatan administrasi keanggotaan, deposit koleksi khusus, bebas pustaka, dan kuitansi digital
          </p>
        </div>

        <div className="flex items-center gap-2.5 self-start">
          <button
            type="button"
            onClick={() => {
              setFormError(null);
              setIsNewTrxModalOpen(true);
            }}
            className="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-[#FF6B35] hover:bg-[#e85a25] text-white text-xs font-bold shadow-md transition-colors cursor-pointer whitespace-nowrap"
          >
            <Plus className="w-4 h-4" />
            <span>Catat Transaksi Baru</span>
          </button>
        </div>
      </div>

      {/* Transactions Table Card (Transparent Glacier Glass) */}
      <div className="glacier-card rounded-3xl p-5 space-y-4">
        <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
          <div className="relative flex-1 max-w-md">
            <Search className="w-4 h-4 text-sky-600 dark:text-sky-300 absolute left-3.5 top-1/2 -translate-y-1/2 z-10 pointer-events-none" />
            <input
              type="text"
              value={searchQuery}
              onChange={(e) => setSearchQuery(e.target.value)}
              placeholder="Cari nomor invoice (TRX), anggota, atau rincian transaksi..."
              aria-label="Cari riwayat transaksi"
              className="glacier-pill w-full pl-10 pr-4 py-2 rounded-xl text-xs sm:text-sm text-slate-900 dark:text-white"
            />
          </div>

          <span className="text-xs font-mono-tabular text-slate-700 dark:text-slate-400">
            Menampilkan {filteredTransactions.length} catatan transaksi
          </span>
        </div>

        <div className="overflow-x-auto">
          <table className="w-full text-left border-collapse">
            <thead>
              <tr className="border-b border-slate-200 text-[11px] font-bold text-slate-800">
                <th className="py-3 px-2">NO. INVOICE</th>
                <th className="py-3 px-2">ANGGOTA</th>
                <th className="py-3 px-2">JENIS & RINCIAN TRANSAKSI</th>
                <th className="py-3 px-2">TANGGAL</th>
                <th className="py-3 px-2 text-right">NOMINAL</th>
                <th className="py-3 px-2 text-right">KUITANSI</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-200/70 text-xs">
              {filteredTransactions.map((trx) => (
                <tr
                  key={trx.id}
                  className="hover:bg-slate-50 transition-colors"
                >
                  <td className="py-3 px-2 font-mono-tabular font-bold text-slate-900 whitespace-nowrap">
                    {trx.invoiceCode}
                  </td>

                  <td className="py-3 px-2 whitespace-nowrap">
                    <p className="font-bold text-slate-900">{trx.memberName}</p>
                    <p className="text-[11px] font-mono-tabular text-slate-700">
                      {trx.memberCode}
                    </p>
                  </td>

                  <td className="py-3 px-2 max-w-[200px]">
                    <p className="font-bold text-slate-900 truncate">{trx.type}</p>
                    <p className="text-[11px] text-slate-700 truncate">
                      {trx.description}
                    </p>
                  </td>

                  <td className="py-3 px-2 font-mono-tabular font-medium text-slate-800 whitespace-nowrap">
                    {trx.date}
                  </td>

                  <td className="py-3 px-2 text-right font-mono-tabular font-extrabold text-slate-900 whitespace-nowrap">
                    {formatIdr(trx.amount)}
                  </td>

                  <td className="py-3 px-2 text-right whitespace-nowrap">
                    <button
                      type="button"
                      onClick={() => setSelectedReceipt(trx)}
                      aria-label={`Lihat kuitansi ${trx.invoiceCode}`}
                      className="glacier-pill inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl hover:bg-sky-50 text-slate-800 text-xs font-bold transition-colors cursor-pointer"
                    >
                      <Receipt className="w-3.5 h-3.5 text-sky-700" />
                      <span>Kuitansi</span>
                    </button>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>

      {/* Modal Kuitansi Digital */}
      {selectedReceipt && (
        <div
          role="dialog"
          aria-modal="true"
          aria-labelledby="modal-receipt-title"
          className="fixed inset-0 z-50 bg-black/65 backdrop-blur-xs flex items-center justify-center p-4"
        >
          <div className="w-full max-w-md rounded-3xl bg-white dark:bg-[#1E1C3A] border border-slate-200 dark:border-white/15 p-6 shadow-2xl space-y-4">
            <div className="flex items-center justify-between border-b border-slate-200 dark:border-white/10 pb-3">
              <div>
                <h2
                  id="modal-receipt-title"
                  className="text-base font-bold text-slate-900 dark:text-white"
                >
                  Kuitansi Resmi BiblioVerse
                </h2>
                <p className="text-[11px] font-mono-tabular text-slate-700 dark:text-slate-400">
                  {selectedReceipt.invoiceCode} · Terverifikasi Lunas
                </p>
              </div>
              <CheckCircle2 className="w-7 h-7 text-emerald-500" />
            </div>

            <div className="space-y-2.5 text-xs">
              <div className="flex justify-between">
                <span className="text-slate-700 dark:text-slate-400">Nama Anggota</span>
                <span className="font-bold text-slate-900 dark:text-white">
                  {selectedReceipt.memberName} ({selectedReceipt.memberCode})
                </span>
              </div>
              <div className="flex justify-between">
                <span className="text-slate-700 dark:text-slate-400">Jenis Layanan</span>
                <span className="font-semibold text-slate-800 dark:text-slate-200">
                  {selectedReceipt.type}
                </span>
              </div>
              <div className="flex justify-between">
                <span className="text-slate-700 dark:text-slate-400">Metode Bayar</span>
                <span className="font-semibold text-slate-800 dark:text-slate-200">
                  {selectedReceipt.paymentMethod}
                </span>
              </div>
              <div className="flex justify-between">
                <span className="text-slate-700 dark:text-slate-400">Tanggal</span>
                <span className="font-mono-tabular text-slate-800 dark:text-slate-200">
                  {selectedReceipt.date}
                </span>
              </div>
              <div className="pt-2 border-t border-slate-200 dark:border-white/10 flex justify-between items-baseline">
                <span className="font-bold text-slate-700 dark:text-slate-300">Total Dibayar</span>
                <span className="text-lg font-extrabold font-mono-tabular text-emerald-600 dark:text-emerald-400">
                  {formatIdr(selectedReceipt.amount)}
                </span>
              </div>
            </div>

            <div className="flex justify-end pt-2">
              <button
                type="button"
                onClick={() => setSelectedReceipt(null)}
                className="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold cursor-pointer"
              >
                Tutup Kuitansi
              </button>
            </div>
          </div>
        </div>
      )}

      {/* Modal Buat Transaksi Baru */}
      {isNewTrxModalOpen && (
        <div
          role="dialog"
          aria-modal="true"
          aria-labelledby="modal-new-trx-title"
          className="fixed inset-0 z-50 bg-black/65 backdrop-blur-xs flex items-center justify-center p-4"
        >
          <div className="w-full max-w-md rounded-3xl bg-white dark:bg-[#1E1C3A] border border-slate-200 dark:border-white/15 p-6 shadow-2xl">
            <h2
              id="modal-new-trx-title"
              className="text-lg font-bold text-slate-900 dark:text-white"
            >
              Catat Transaksi Administrasi Baru
            </h2>
            <p className="text-xs text-slate-700 dark:text-slate-400 mt-1">
              Tagihan akan tercatat untuk anggota aktif: {activeMember.name}
            </p>

            <form onSubmit={handleCreateNewTransaction} className="mt-4 space-y-4">
              {formError && (
                <div className="p-3 rounded-xl bg-rose-500/15 border border-rose-500/30 text-xs font-semibold text-rose-600 dark:text-rose-300 flex items-center gap-2">
                  <AlertCircle className="w-4 h-4 shrink-0" />
                  <span>{formError}</span>
                </div>
              )}

              <div>
                <label
                  htmlFor="trx-type-select"
                  className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1"
                >
                  Jenis Transaksi
                </label>
                <select
                  id="trx-type-select"
                  value={trxType}
                  onChange={(e) => setTrxType(e.target.value as TransactionRecord['type'])}
                  className="w-full px-3.5 py-2 rounded-xl bg-slate-100 dark:bg-slate-900 border border-slate-200 dark:border-white/10 text-xs sm:text-sm text-slate-900 dark:text-white"
                >
                  <option value="Perpanjangan Keanggotaan">Perpanjangan Keanggotaan</option>
                  <option value="Denda Keterlambatan">Denda Keterlambatan</option>
                  <option value="Deposit Koleksi Khusus">Deposit Koleksi Khusus</option>
                  <option value="Bebas Pustaka & Legalisir">Bebas Pustaka & Legalisir</option>
                </select>
              </div>

              <div>
                <label
                  htmlFor="trx-desc-input"
                  className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1"
                >
                  Keterangan / Rincian Layanan
                </label>
                <input
                  id="trx-desc-input"
                  type="text"
                  value={trxDesc}
                  onChange={(e) => setTrxDesc(e.target.value)}
                  placeholder="Contoh: Legalisir Surat Bebas Pustaka Semester Ganjil"
                  className="w-full px-3.5 py-2 rounded-xl bg-slate-100 dark:bg-slate-900 border border-slate-200 dark:border-white/10 text-xs sm:text-sm text-slate-900 dark:text-white"
                />
              </div>

              <div>
                <label
                  htmlFor="trx-amount-input"
                  className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1"
                >
                  Nominal (Rp)
                </label>
                <input
                  id="trx-amount-input"
                  type="number"
                  value={trxAmount}
                  onChange={(e) => setTrxAmount(e.target.value)}
                  className="w-full px-3.5 py-2 rounded-xl bg-slate-100 dark:bg-slate-900 border border-slate-200 dark:border-white/10 text-xs sm:text-sm text-slate-900 dark:text-white font-mono-tabular"
                />
              </div>

              <div className="flex items-center justify-end gap-2 pt-3 border-t border-slate-200 dark:border-white/10">
                <button
                  type="button"
                  onClick={() => setIsNewTrxModalOpen(false)}
                  className="px-4 py-2 rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-white/10 cursor-pointer"
                >
                  Batal
                </button>
                <button
                  type="submit"
                  className="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold shadow-md cursor-pointer"
                >
                  Simpan Tagihan
                </button>
              </div>
            </form>
          </div>
        </div>
      )}
    </div>
  );
};
