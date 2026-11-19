import React, { useState } from 'react';
import {
  X,
  Eye,
  EyeOff,
  Copy,
  Check,
  QrCode,
  Barcode,
  ShieldCheck,
  Sparkles,
  Building2,
  ScanLine,
} from 'lucide-react';
import { type Member, maskMemberIdentity } from '../utils/libraryLogic';

interface DigitalMemberCardModalProps {
  isOpen: boolean;
  member: Member;
  onClose: () => void;
}

export const DigitalMemberCardModal: React.FC<DigitalMemberCardModalProps> = ({
  isOpen,
  member,
  onClose,
}) => {
  const [isIdentityRevealed, setIsIdentityRevealed] = useState(false);
  const [codeFormat, setCodeFormat] = useState<'qr' | 'barcode'>('qr');
  const [copiedState, setCopiedState] = useState(false);

  if (!isOpen) return null;

  const rawIdentityNumber = member.nikId || '3173042608990001';
  const nppCode = member.nppNumber || 'NPP-3173-0102-2026';
  const maskedOrFullNumber = maskMemberIdentity(rawIdentityNumber, isIdentityRevealed);

  const handleCopyNumber = () => {
    if (typeof navigator !== 'undefined' && navigator.clipboard) {
      navigator.clipboard.writeText(rawIdentityNumber).catch(() => {});
    }
    setCopiedState(true);
    setTimeout(() => setCopiedState(false), 2200);
  };

  // Deterministic 15x15 QR Matrix pattern for authentic 2D QR code visual
  const qrSize = 15;
  const isFinderCell = (r: number, c: number) => {
    const inTopLeft = r < 4 && c < 4;
    const inTopRight = r < 4 && c >= qrSize - 4;
    const inBottomLeft = r >= qrSize - 4 && c < 4;
    return inTopLeft || inTopRight || inBottomLeft;
  };

  const getCellFill = (r: number, c: number) => {
    const seed = (r * 31 + c * 17 + member.memberCode.charCodeAt(c % member.memberCode.length)) % 7;
    return seed === 0 || seed === 2 || seed === 5;
  };

  // Deterministic Code-128 1D Barcode bar widths
  const barcodeBars: { width: number; dark: boolean }[] = [
    { width: 3, dark: true },
    { width: 1, dark: false },
    { width: 2, dark: true },
    { width: 2, dark: false },
    { width: 1, dark: true },
    { width: 3, dark: false },
    { width: 2, dark: true },
    { width: 1, dark: false },
    { width: 4, dark: true },
    { width: 1, dark: false },
    { width: 1, dark: true },
    { width: 2, dark: false },
    { width: 3, dark: true },
    { width: 2, dark: false },
    { width: 1, dark: true },
    { width: 1, dark: false },
    { width: 3, dark: true },
    { width: 1, dark: false },
    { width: 2, dark: true },
    { width: 3, dark: false },
    { width: 1, dark: true },
    { width: 2, dark: false },
    { width: 4, dark: true },
    { width: 1, dark: false },
    { width: 2, dark: true },
    { width: 1, dark: false },
    { width: 2, dark: true },
    { width: 2, dark: false },
    { width: 3, dark: true },
    { width: 1, dark: false },
    { width: 1, dark: true },
    { width: 2, dark: false },
    { width: 3, dark: true },
    { width: 1, dark: false },
    { width: 2, dark: true },
  ];

  return (
    <div
      role="dialog"
      aria-modal="true"
      aria-labelledby="digital-member-card-title"
      className="fixed inset-0 z-50 bg-black/75 backdrop-blur-md flex items-center justify-center p-4"
    >
      <div className="relative w-full max-w-md rounded-[32px] bg-gradient-to-br from-[#231D58]/95 via-[#1A1642]/95 to-[#120F2E]/95 backdrop-blur-2xl border border-white/25 text-white shadow-[0_24px_80px_rgba(0,0,0,0.65)] overflow-hidden">
        {/* Ambient Decorative Glows */}
        <div
          aria-hidden="true"
          className="pointer-events-none absolute -top-20 -right-16 w-48 h-48 rounded-full bg-amber-400/20 blur-3xl"
        />
        <div
          aria-hidden="true"
          className="pointer-events-none absolute -bottom-20 -left-16 w-48 h-48 rounded-full bg-indigo-500/25 blur-3xl"
        />

        {/* Top Modal Header */}
        <div className="relative z-10 px-6 pt-6 pb-4 border-b border-white/15 flex items-center justify-between">
          <div className="flex items-center gap-2.5">
            <div className="w-9 h-9 rounded-2xl bg-white/15 backdrop-blur-md border border-white/25 flex items-center justify-center text-amber-300 font-extrabold text-sm">
              B
            </div>
            <div>
              <h2
                id="digital-member-card-title"
                className="text-base font-extrabold tracking-tight text-white"
              >
                Kartu Anggota Digital
              </h2>
              <p className="text-[11px] text-indigo-200">
                BiblioVerse Pass · Akses Terpadu Seluruh Cabang
              </p>
            </div>
          </div>

          <button
            type="button"
            onClick={onClose}
            aria-label="Tutup Kartu Anggota Digital"
            data-testid="close-digital-card-btn"
            className="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 border border-white/20 flex items-center justify-center text-white transition-colors cursor-pointer"
          >
            <X className="w-4 h-4" />
          </button>
        </div>

        {/* Member Identity Strip + Masked Number with Eye Reveal & Copy */}
        <div className="relative z-10 px-6 pt-5 space-y-4">
          <div className="flex items-center gap-3.5">
            <div className="relative shrink-0">
              <div
                className={`w-14 h-14 rounded-2xl bg-gradient-to-br ${member.avatarBg} flex items-center justify-center text-white font-extrabold text-lg ring-2 ring-white/30 shadow-lg`}
              >
                {member.avatarInitials}
              </div>
              <span
                title="Identitas Terverifikasi"
                className="absolute -bottom-1 -right-1 w-5 h-5 rounded-full bg-emerald-500 border-2 border-[#1A1642] flex items-center justify-center"
              >
                <ShieldCheck className="w-3 h-3 text-white" />
              </span>
            </div>

            <div className="min-w-0 flex-1">
              <div className="flex flex-wrap items-center gap-1.5">
                <span className="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-500/20 border border-emerald-400/35 text-emerald-300 text-[10px] font-bold">
                  <ShieldCheck className="w-3 h-3" />
                  Terverifikasi
                </span>
                <span className="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-amber-400/20 border border-amber-300/35 text-amber-200 text-[10px] font-bold">
                  <Sparkles className="w-3 h-3" />
                  {member.role}
                </span>
              </div>
              <p className="text-lg font-extrabold text-white truncate mt-1">{member.name}</p>
              <p className="text-xs text-indigo-200 truncate">{member.department}</p>
            </div>
          </div>

          {/* Masked Member ID Box with Eye Reveal & Copy Button */}
          <div className="rounded-2xl bg-white/10 backdrop-blur-xl border border-white/20 p-3.5 flex items-center justify-between gap-2">
            <div>
              <p className="text-[10px] font-bold uppercase tracking-wider text-indigo-200">
                Nomor Identitas Anggota (NIK / ID)
              </p>
              <p
                data-testid="digital-card-masked-id"
                className="text-sm sm:text-base font-extrabold font-mono-tabular text-amber-300 tracking-wider mt-0.5"
              >
                {maskedOrFullNumber}
              </p>
            </div>

            <div className="flex items-center gap-1.5">
              <button
                type="button"
                onClick={() => setIsIdentityRevealed((prev) => !prev)}
                data-testid="toggle-reveal-id-btn"
                aria-label={
                  isIdentityRevealed ? 'Sembunyikan nomor anggota' : 'Intip nomor anggota penuh'
                }
                title={isIdentityRevealed ? 'Sembunyikan Nomor' : 'Intip Nomor Penuh'}
                className="p-2 rounded-xl bg-white/15 hover:bg-white/25 border border-white/20 text-white transition-colors cursor-pointer"
              >
                {isIdentityRevealed ? (
                  <EyeOff className="w-4 h-4 text-amber-300" />
                ) : (
                  <Eye className="w-4 h-4 text-indigo-200" />
                )}
              </button>

              <button
                type="button"
                onClick={handleCopyNumber}
                data-testid="copy-member-id-btn"
                aria-label="Salin nomor anggota"
                title="Salin Nomor Anggota"
                className="inline-flex items-center gap-1 px-2.5 py-2 rounded-xl bg-white/15 hover:bg-white/25 border border-white/20 text-xs font-bold text-white transition-colors cursor-pointer"
              >
                {copiedState ? (
                  <>
                    <Check className="w-3.5 h-3.5 text-emerald-400" />
                    <span className="text-emerald-300">Tersalin</span>
                  </>
                ) : (
                  <>
                    <Copy className="w-3.5 h-3.5 text-indigo-200" />
                    <span>Salin</span>
                  </>
                )}
              </button>
            </div>
          </div>
        </div>

        {/* Center Visual Scanner Area: 2D QR Code OR 1D Code-128 Barcode */}
        <div className="relative z-10 px-6 py-5 flex flex-col items-center">
          <div className="w-full rounded-3xl bg-white text-slate-950 p-5 shadow-inner border-4 border-amber-300/40 flex flex-col items-center justify-center min-h-[230px]">
            {codeFormat === 'qr' ? (
              <div
                data-testid="visual-qr-code"
                className="flex flex-col items-center justify-center"
              >
                <div className="relative p-3 rounded-2xl border-2 border-slate-900/15 bg-white shadow-xs">
                  <svg
                    viewBox={`0 0 ${qrSize} ${qrSize}`}
                    className="w-40 h-40 sm:w-44 sm:h-44"
                    aria-label="QR Code Kartu Anggota Digital"
                  >
                    {/* 3 Finder Patterns (7x7 equivalent scaled into 3.5x3.5 corners) */}
                    <rect x="0" y="0" width="3.5" height="3.5" rx="0.5" fill="#0F172A" />
                    <rect x="0.6" y="0.6" width="2.3" height="2.3" rx="0.3" fill="#FFFFFF" />
                    <rect x="1.1" y="1.1" width="1.3" height="1.3" rx="0.2" fill="#0F172A" />

                    <rect x={qrSize - 3.5} y="0" width="3.5" height="3.5" rx="0.5" fill="#0F172A" />
                    <rect
                      x={qrSize - 2.9}
                      y="0.6"
                      width="2.3"
                      height="2.3"
                      rx="0.3"
                      fill="#FFFFFF"
                    />
                    <rect
                      x={qrSize - 2.4}
                      y="1.1"
                      width="1.3"
                      height="1.3"
                      rx="0.2"
                      fill="#0F172A"
                    />

                    <rect x="0" y={qrSize - 3.5} width="3.5" height="3.5" rx="0.5" fill="#0F172A" />
                    <rect
                      x="0.6"
                      y={qrSize - 2.9}
                      width="2.3"
                      height="2.3"
                      rx="0.3"
                      fill="#FFFFFF"
                    />
                    <rect
                      x="1.1"
                      y={qrSize - 2.4}
                      width="1.3"
                      height="1.3"
                      rx="0.2"
                      fill="#0F172A"
                    />

                    {/* Data Matrix Modules */}
                    {Array.from({ length: qrSize }).map((_, r) =>
                      Array.from({ length: qrSize }).map((__, c) => {
                        if (isFinderCell(r, c)) return null;
                        if (!getCellFill(r, c)) return null;
                        return (
                          <rect
                            key={`${r}-${c}`}
                            x={c + 0.1}
                            y={r + 0.1}
                            width="0.8"
                            height="0.8"
                            rx="0.18"
                            fill="#0F172A"
                          />
                        );
                      })
                    )}
                  </svg>
                </div>
                <p className="mt-2.5 text-[11px] font-mono-tabular font-bold text-slate-700">
                  QR 2D · {member.memberCode}
                </p>
              </div>
            ) : (
              <div
                data-testid="visual-barcode-1d"
                className="w-full flex flex-col items-center justify-center py-3"
              >
                <div className="w-full max-w-[290px] p-4 rounded-2xl border-2 border-slate-900/15 bg-white flex flex-col items-center">
                  <div
                    aria-label="Barcode 1D Code 128 Kartu Anggota"
                    className="flex items-stretch justify-center h-24 w-full gap-[2px]"
                  >
                    {barcodeBars.map((bar, i) => (
                      <span
                        key={i}
                        className={bar.dark ? 'bg-slate-950' : 'bg-transparent'}
                        style={{ flex: bar.width }}
                      />
                    ))}
                  </div>
                  <p className="mt-3 text-xs font-mono-tabular font-extrabold tracking-[0.22em] text-slate-900">
                    {member.memberCode.replace(/-/g, '')}
                  </p>
                </div>
                <p className="mt-2 text-[11px] font-mono-tabular font-bold text-slate-700">
                  CODE-128 1D · Siap Pindai Laser Gate & Sirkulasi
                </p>
              </div>
            )}
          </div>

          {/* Interactive Toggle Button: Tampilkan Barcode / Tampilkan QR Code */}
          <button
            type="button"
            onClick={() => setCodeFormat((prev) => (prev === 'qr' ? 'barcode' : 'qr'))}
            data-testid="toggle-code-format-btn"
            className="mt-4 w-full py-3 px-4 rounded-2xl bg-[#FF6B35] hover:bg-[#e85a25] text-white text-xs sm:text-sm font-extrabold shadow-lg flex items-center justify-center gap-2 transition-all cursor-pointer"
          >
            {codeFormat === 'qr' ? (
              <>
                <Barcode className="w-4 h-4" />
                <span>Tampilkan Barcode</span>
              </>
            ) : (
              <>
                <QrCode className="w-4 h-4" />
                <span>Tampilkan QR Code</span>
              </>
            )}
          </button>
        </div>

        {/* Bottom NPP & Physical Facility Usage Instructions */}
        <div className="relative z-10 px-6 pb-6 pt-3 border-t border-white/15 bg-black/20 space-y-2.5">
          <div className="flex items-center justify-between text-xs">
            <span className="inline-flex items-center gap-1.5 text-indigo-200 font-semibold">
              <Building2 className="w-3.5 h-3.5 text-amber-300" />
              Nomor Pokok Perpustakaan (NPP)
            </span>
            <span
              data-testid="digital-card-npp"
              className="font-mono-tabular font-extrabold text-amber-300"
            >
              {nppCode}
            </span>
          </div>

          <div className="rounded-xl bg-white/[0.07] border border-white/10 p-3 flex items-start gap-2.5 text-[11px] text-indigo-100/90 leading-relaxed">
            <ScanLine className="w-4 h-4 text-amber-300 shrink-0 mt-0.5" />
            <p>
              Arahkan kode di atas pada pemindai pintu putar (<em>turnstile gate</em>), anjungan
              mandiri (<em>Self-Check Kiosk</em>), atau meja pustakawan untuk akses ruang baca,
              loker pintar, dan peminjaman fisik.
            </p>
          </div>
        </div>
      </div>
    </div>
  );
};
