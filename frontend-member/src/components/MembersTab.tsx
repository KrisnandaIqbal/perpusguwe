import React, { useState } from 'react';
import {
  UserPlus,
  Search,
  Award,
  CheckCircle2,
  AlertTriangle,
  QrCode,
  ShieldCheck,
  Mail,
  Phone,
  Building2,
  Calendar,
} from 'lucide-react';
import { type Member, formatMinutesToHoursAndMinutes } from '../utils/libraryLogic';

interface MembersTabProps {
  members: Member[];
  activeMember: Member;
  onSelectActiveMember: (memberId: string) => void;
  onAddMember: (newMember: Omit<Member, 'id' | 'badges'>) => void;
}

export const MembersTab: React.FC<MembersTabProps> = ({
  members,
  activeMember,
  onSelectActiveMember,
  onAddMember,
}) => {
  const [searchQuery, setSearchQuery] = useState('');
  const [statusFilter, setStatusFilter] = useState<'Semua' | Member['status']>('Semua');
  const [isModalOpen, setIsModalOpen] = useState(false);

  // New Member Form State
  const [name, setName] = useState('');
  const [email, setEmail] = useState('');
  const [phone, setPhone] = useState('');
  const [department, setDepartment] = useState('');
  const [role, setRole] = useState<Member['role']>('Mahasiswa');
  const [formError, setFormError] = useState<string | null>(null);

  const filteredMembers = members.filter((m) => {
    if (statusFilter !== 'Semua' && m.status !== statusFilter) return false;
    if (!searchQuery.trim()) return true;
    const q = searchQuery.toLowerCase();
    return (
      m.name.toLowerCase().includes(q) ||
      m.memberCode.toLowerCase().includes(q) ||
      m.department.toLowerCase().includes(q) ||
      m.email.toLowerCase().includes(q)
    );
  });

  const handleCreateMember = (e: React.FormEvent) => {
    e.preventDefault();
    if (!name.trim() || !email.trim() || !department.trim()) {
      setFormError('Nama lengkap, email institusi, dan fakultas/unit wajib diisi.');
      return;
    }
    const initials = name
      .trim()
      .split(' ')
      .map((p) => p[0])
      .join('')
      .toUpperCase()
      .slice(0, 2);

    const codeNum = 2200 + members.length + 1;
    onAddMember({
      memberCode: `BIB-2026-${codeNum}`,
      name: name.trim(),
      role,
      email: email.trim(),
      phone: phone.trim() || '0812-0000-0000',
      department: department.trim(),
      joinDate: '2026-11-09',
      expiryDate: '2028-11-09',
      status: 'Aktif',
      maxQuota: role === 'Peneliti' ? 6 : 4,
      currentBorrowed: 0,
      readingLevel: 1,
      readerPoints: 100,
      nextLevelPoints: 500,
      dayStreak: 1,
      readMinutesTotal: 0,
      rankGlobal: 9950,
      percentile: 'Top 15%',
      avatarBg: 'from-indigo-500 via-purple-600 to-pink-600',
      avatarInitials: initials || 'AN',
    });

    setName('');
    setEmail('');
    setPhone('');
    setDepartment('');
    setFormError(null);
    setIsModalOpen(false);
  };

  return (
    <div className="space-y-6">
      {/* Header & Primary Action */}
      <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h1 className="text-2xl font-bold text-slate-900 dark:text-white">
            Data Anggota & Kartu Perpustakaan Digital
          </h1>
          <p className="text-xs sm:text-sm text-slate-700 dark:text-slate-300">
            Kelola profil keanggotaan, kuota peminjaman, status bebas pustaka, dan lencana literasi
          </p>
        </div>

        <button
          type="button"
          onClick={() => {
            setFormError(null);
            setIsModalOpen(true);
          }}
          className="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#FF6B35] hover:bg-[#e85a25] text-white text-xs font-bold shadow-md transition-colors cursor-pointer whitespace-nowrap self-start"
        >
          <UserPlus className="w-4 h-4" />
          <span>Registrasi Anggota Baru</span>
        </button>
      </div>

      {/* Active Member Digital Library Card + Bento Quick Summary */}
      <div className="grid grid-cols-1 lg:grid-cols-12 gap-5">
        {/* Digital Library Smart Card */}
        <div className="lg:col-span-7 rounded-3xl p-6 bg-gradient-to-br from-[#2B2467] via-[#1E194D] to-[#120E31] text-white border border-white/15 shadow-xl relative overflow-hidden flex flex-col justify-between">
          <div className="flex items-start justify-between gap-4 relative z-10">
            <div className="flex items-center gap-4">
              <div
                className={`w-16 h-16 rounded-2xl bg-gradient-to-br ${activeMember.avatarBg} flex items-center justify-center text-xl font-extrabold text-white shadow-lg ring-2 ring-white/25`}
              >
                {activeMember.avatarInitials}
              </div>
              <div>
                <div className="flex items-center gap-2 text-xs text-amber-300 font-semibold">
                  <span>{activeMember.role}</span>
                  <span aria-hidden="true">·</span>
                  <span className="font-mono-tabular">{activeMember.memberCode}</span>
                </div>
                <h2 className="text-2xl font-bold tracking-tight text-white mt-0.5">
                  {activeMember.name}
                </h2>
                <p className="text-xs text-slate-300 mt-0.5">{activeMember.department}</p>
              </div>
            </div>

            <div className="flex flex-col items-end">
              <QrCode className="w-10 h-10 text-white/80" />
              <span className="text-[10px] font-mono-tabular text-slate-300 mt-1">
                PASS-ID VERIFIED
              </span>
            </div>
          </div>

          <div className="grid grid-cols-2 sm:grid-cols-4 gap-3 my-6 pt-5 border-t border-white/10 relative z-10">
            <div>
              <p className="text-[11px] text-slate-400">Status Anggota</p>
              <p className="text-xs sm:text-sm font-bold text-emerald-300 mt-0.5 whitespace-nowrap">
                {activeMember.status}
              </p>
            </div>
            <div>
              <p className="text-[11px] text-slate-400">Kuota Sirkulasi</p>
              <p className="text-xs sm:text-sm font-bold font-mono-tabular text-white mt-0.5 whitespace-nowrap">
                {activeMember.currentBorrowed} / {activeMember.maxQuota} Buku
              </p>
            </div>
            <div>
              <p className="text-[11px] text-slate-400">Total Jam Baca</p>
              <p className="text-xs sm:text-sm font-bold font-mono-tabular text-amber-300 mt-0.5 whitespace-nowrap">
                {formatMinutesToHoursAndMinutes(activeMember.readMinutesTotal)} jam
              </p>
            </div>
            <div>
              <p className="text-[11px] text-slate-400">Berlaku Hingga</p>
              <p className="text-xs sm:text-sm font-bold font-mono-tabular text-white mt-0.5 whitespace-nowrap">
                {activeMember.expiryDate}
              </p>
            </div>
          </div>

          <div className="flex flex-wrap items-center justify-between gap-2 text-xs text-slate-300 relative z-10">
            <div className="flex items-center gap-4">
              <span className="inline-flex items-center gap-1.5">
                <Mail className="w-3.5 h-3.5 text-indigo-300" />
                {activeMember.email}
              </span>
              <span className="inline-flex items-center gap-1.5 font-mono-tabular">
                <Phone className="w-3.5 h-3.5 text-indigo-300" />
                {activeMember.phone}
              </span>
            </div>
            <span className="font-mono-tabular text-[11px] text-white/70">
              Peringkat #{activeMember.rankGlobal} ({activeMember.percentile})
            </span>
          </div>
        </div>

        {/* Member Reading Achievements & Quota Breakdown */}
        <div className="lg:col-span-5 grid grid-cols-2 gap-4">
          <div className="rounded-3xl bg-[#FDE047] text-slate-950 p-5 flex flex-col justify-between">
            <span className="text-xs font-extrabold tracking-wide">POIN LITERASI</span>
            <p className="text-3xl font-extrabold font-mono-tabular my-2">
              {activeMember.readerPoints.toLocaleString('en-US')}
            </p>
            <p className="text-xs text-slate-800 font-medium">
              Level {activeMember.readingLevel} · {activeMember.percentile} Pembaca
            </p>
          </div>

          <div className="rounded-3xl bg-[#A3E635] text-slate-950 p-5 flex flex-col justify-between">
            <span className="text-xs font-extrabold tracking-wide">STREAK KUNJUNGAN</span>
            <p className="text-3xl font-extrabold font-mono-tabular my-2">
              {activeMember.dayStreak} Hari
            </p>
            <p className="text-xs text-slate-800 font-medium">
              Aktif membaca & meminjam koleksi
            </p>
          </div>

          <div className="col-span-2 rounded-3xl bg-white dark:bg-[#1E1C3A] border border-slate-200 dark:border-white/10 p-5 flex flex-col justify-between">
            <div className="flex items-center justify-between">
              <span className="text-xs font-bold text-slate-700 dark:text-slate-200">
                Koleksi Lencana Anggota ({activeMember.badges.length})
              </span>
              <Award className="w-4 h-4 text-amber-500" />
            </div>
            <div className="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-2">
              {activeMember.badges.slice(0, 4).map((b) => (
                <div
                  key={b.id}
                  className="p-2.5 rounded-xl bg-slate-50 dark:bg-white/5 border border-slate-200/70 dark:border-white/10"
                >
                  <p className="text-xs font-bold text-slate-900 dark:text-white">{b.name}</p>
                  <p className="text-[11px] text-slate-700 dark:text-slate-300 truncate mt-0.5">
                    {b.description}
                  </p>
                </div>
              ))}
            </div>
          </div>
        </div>
      </div>

      {/* Directory Search & Filter Bar */}
      <div className="rounded-3xl bg-white dark:bg-[#1E1C3A] border border-slate-200 dark:border-white/10 p-5 shadow-sm">
        <div className="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-5">
          <div className="relative flex-1 max-w-md">
            <Search className="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" />
            <input
              type="text"
              value={searchQuery}
              onChange={(e) => setSearchQuery(e.target.value)}
              placeholder="Cari nama anggota, kode BIB, email, atau fakultas..."
              aria-label="Cari anggota perpustakaan"
              className="w-full pl-10 pr-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-900/80 border border-slate-200 dark:border-white/10 text-xs sm:text-sm text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500"
            />
          </div>

          <div className="flex flex-wrap items-center gap-1.5 p-1 rounded-xl bg-slate-100 dark:bg-slate-900/80">
            {(['Semua', 'Aktif', 'Bebas Pustaka', 'Tertahan Denda'] as const).map((status) => (
              <button
                key={status}
                type="button"
                onClick={() => setStatusFilter(status)}
                className={`px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors cursor-pointer whitespace-nowrap ${
                  statusFilter === status
                    ? 'bg-indigo-600 text-white shadow-xs'
                    : 'text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white'
                }`}
              >
                {status}
              </button>
            ))}
          </div>
        </div>

        {/* Members Table */}
        <div className="overflow-x-auto">
          <table className="w-full text-left border-collapse">
            <thead>
              <tr className="border-b border-slate-200 dark:border-white/10 text-[11px] font-bold text-slate-700 dark:text-slate-300">
                <th className="py-3 px-3">ANGGOTA</th>
                <th className="py-3 px-3">KODE ID</th>
                <th className="py-3 px-3">FAKULTAS / UNIT</th>
                <th className="py-3 px-3 text-right">PINJAMAN</th>
                <th className="py-3 px-3 text-right">POIN & LEVEL</th>
                <th className="py-3 px-3">STATUS</th>
                <th className="py-3 px-3 text-right">AKSI</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-200/60 dark:divide-white/5 text-xs">
              {filteredMembers.map((member) => {
                const isCurrent = member.id === activeMember.id;
                return (
                  <tr
                    key={member.id}
                    className={`transition-colors ${
                      isCurrent
                        ? 'bg-indigo-50/80 dark:bg-indigo-500/15'
                        : 'hover:bg-slate-50 dark:hover:bg-white/5'
                    }`}
                  >
                    <td className="py-3.5 px-3">
                      <div className="flex items-center gap-3">
                        <div
                          className={`w-9 h-9 rounded-full bg-gradient-to-br ${member.avatarBg} text-white font-bold text-xs flex items-center justify-center shrink-0`}
                        >
                          {member.avatarInitials}
                        </div>
                        <div>
                          <p className="font-bold text-slate-900 dark:text-white">{member.name}</p>
                          <p className="text-[11px] text-slate-700 dark:text-slate-300">
                            {member.role} · {member.email}
                          </p>
                        </div>
                      </div>
                    </td>
                    <td className="py-3.5 px-3 font-mono-tabular font-semibold text-slate-700 dark:text-slate-200 whitespace-nowrap">
                      {member.memberCode}
                    </td>
                    <td className="py-3.5 px-3 text-slate-700 dark:text-slate-300 max-w-[200px] truncate">
                      {member.department}
                    </td>
                    <td className="py-3.5 px-3 text-right font-mono-tabular font-semibold text-slate-800 dark:text-slate-200">
                      {member.currentBorrowed} / {member.maxQuota}
                    </td>
                    <td className="py-3.5 px-3 text-right font-mono-tabular text-slate-700 dark:text-slate-300">
                      Lv.{member.readingLevel} · {member.readerPoints.toLocaleString('en-US')} pts
                    </td>
                    <td className="py-3.5 px-3 whitespace-nowrap">
                      <span
                        className={`inline-flex items-center gap-1 font-semibold ${
                          member.status === 'Aktif'
                            ? 'text-emerald-600 dark:text-emerald-400'
                            : member.status === 'Bebas Pustaka'
                            ? 'text-sky-600 dark:text-sky-400'
                            : 'text-rose-600 dark:text-rose-400'
                        }`}
                      >
                        {member.status === 'Tertahan Denda' ? (
                          <AlertTriangle className="w-3.5 h-3.5" />
                        ) : (
                          <CheckCircle2 className="w-3.5 h-3.5" />
                        )}
                        <span>{member.status}</span>
                      </span>
                    </td>
                    <td className="py-3.5 px-3 text-right whitespace-nowrap">
                      <button
                        type="button"
                        onClick={() => onSelectActiveMember(member.id)}
                        className={`px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors cursor-pointer ${
                          isCurrent
                            ? 'bg-emerald-500/20 text-emerald-700 dark:text-emerald-300'
                            : 'bg-slate-200/80 dark:bg-white/10 hover:bg-indigo-600 hover:text-white text-slate-800 dark:text-white'
                        }`}
                      >
                        {isCurrent ? 'Profil Aktif' : 'Pilih Profil'}
                      </button>
                    </td>
                  </tr>
                );
              })}
            </tbody>
          </table>
        </div>
      </div>

      {/* Modal Registrasi Anggota Baru */}
      {isModalOpen && (
        <div
          role="dialog"
          aria-modal="true"
          aria-labelledby="modal-register-member-title"
          className="fixed inset-0 z-50 bg-black/65 backdrop-blur-xs flex items-center justify-center p-4"
        >
          <div className="w-full max-w-md rounded-3xl bg-white dark:bg-[#1E1C3A] border border-slate-200 dark:border-white/15 p-6 shadow-2xl">
            <h2
              id="modal-register-member-title"
              className="text-lg font-bold text-slate-900 dark:text-white"
            >
              Registrasi Anggota Perpustakaan Baru
            </h2>
            <p className="text-xs text-slate-700 dark:text-slate-300 mt-1">
              Kartu anggota digital akan langsung diterbitkan beserta hak pinjam OPAC.
            </p>

            <form onSubmit={handleCreateMember} className="mt-4 space-y-4">
              {formError && (
                <div className="p-3 rounded-xl bg-rose-500/15 border border-rose-500/30 text-xs font-semibold text-rose-600 dark:text-rose-300">
                  {formError}
                </div>
              )}

              <div>
                <label
                  htmlFor="member-name-input"
                  className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1"
                >
                  Nama Lengkap Anggota
                </label>
                <input
                  id="member-name-input"
                  type="text"
                  value={name}
                  onChange={(e) => setName(e.target.value)}
                  placeholder="Contoh: Raka Pratama"
                  className="w-full px-3.5 py-2 rounded-xl bg-slate-100 dark:bg-slate-900 border border-slate-200 dark:border-white/10 text-xs sm:text-sm text-slate-900 dark:text-white"
                />
              </div>

              <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                  <label
                    htmlFor="member-email-input"
                    className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1"
                  >
                    Email Institusi
                  </label>
                  <input
                    id="member-email-input"
                    type="email"
                    value={email}
                    onChange={(e) => setEmail(e.target.value)}
                    placeholder="raka@biblioversity.ac.id"
                    className="w-full px-3.5 py-2 rounded-xl bg-slate-100 dark:bg-slate-900 border border-slate-200 dark:border-white/10 text-xs sm:text-sm text-slate-900 dark:text-white"
                  />
                </div>
                <div>
                  <label
                    htmlFor="member-role-select"
                    className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1"
                  >
                    Kategori Anggota
                  </label>
                  <select
                    id="member-role-select"
                    value={role}
                    onChange={(e) => setRole(e.target.value as Member['role'])}
                    className="w-full px-3.5 py-2 rounded-xl bg-slate-100 dark:bg-slate-900 border border-slate-200 dark:border-white/10 text-xs sm:text-sm text-slate-900 dark:text-white"
                  >
                    <option value="Mahasiswa">Mahasiswa (Kuota 4)</option>
                    <option value="Peneliti">Peneliti (Kuota 6)</option>
                    <option value="Anggota Utama">Anggota Utama (Kuota 5)</option>
                    <option value="Umum">Umum (Kuota 3)</option>
                  </select>
                </div>
              </div>

              <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                  <label
                    htmlFor="member-dept-input"
                    className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1"
                  >
                    Fakultas / Departemen
                  </label>
                  <input
                    id="member-dept-input"
                    type="text"
                    value={department}
                    onChange={(e) => setDepartment(e.target.value)}
                    placeholder="Fakultas Ilmu Komputer"
                    className="w-full px-3.5 py-2 rounded-xl bg-slate-100 dark:bg-slate-900 border border-slate-200 dark:border-white/10 text-xs sm:text-sm text-slate-900 dark:text-white"
                  />
                </div>
                <div>
                  <label
                    htmlFor="member-phone-input"
                    className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1"
                  >
                    Nomor Telepon / WA
                  </label>
                  <input
                    id="member-phone-input"
                    type="text"
                    value={phone}
                    onChange={(e) => setPhone(e.target.value)}
                    placeholder="0812-3456-7890"
                    className="w-full px-3.5 py-2 rounded-xl bg-slate-100 dark:bg-slate-900 border border-slate-200 dark:border-white/10 text-xs sm:text-sm text-slate-900 dark:text-white"
                  />
                </div>
              </div>

              <div className="flex items-center justify-end gap-2 pt-3 border-t border-slate-200 dark:border-white/10">
                <button
                  type="button"
                  onClick={() => setIsModalOpen(false)}
                  className="px-4 py-2 rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-white/10 cursor-pointer"
                >
                  Batal
                </button>
                <button
                  type="submit"
                  className="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold shadow-md cursor-pointer"
                >
                  Simpan & Terbitkan Kartu
                </button>
              </div>
            </form>
          </div>
        </div>
      )}
    </div>
  );
};
