import { api, type MemberProfile, type ActiveLoanItem, type CatalogItem, type LoanHistoryItem, type FineItem } from '../services/portalApi';
import type { Member, BookItem, CirculationRecord, TransactionRecord } from './libraryLogic';

export function mapProfileToMember(profile: MemberProfile): Member {
  return {
    id: profile.id,
    memberCode: profile.memberNo,
    name: profile.name,
    role: (profile.institution.toLowerCase().includes('mahasiswa') ? 'Mahasiswa' : 'Umum') as any,
    email: profile.email,
    phone: profile.phone,
    department: profile.institution,
    joinDate: profile.registerDate,
    expiryDate: profile.expiryDate,
    status: profile.status === 'Active' ? 'Aktif' : 'Tertahan Denda',
    maxQuota: 5,
    currentBorrowed: profile.metrics.activeLoans,
    readingLevel: 3,
    readerPoints: 450 + (profile.metrics.totalHistory * 50),
    nextLevelPoints: 1000,
    dayStreak: 7,
    readMinutesTotal: 120,
    rankGlobal: 14,
    percentile: 'Top 5%',
    avatarBg: 'from-amber-500 to-amber-700',
    avatarInitials: profile.name.slice(0, 2).toUpperCase(),
    nikId: profile.memberNo,
    nppNumber: `INLIS-${profile.memberNo}`,
    address: profile.address,
    joinDateFormatted: profile.registerDate,
    expiryDateFormatted: profile.expiryDate,
    badges: [
      {
        id: 'badge-verified',
        name: 'Anggota Terverifikasi',
        description: 'Terdaftar resmi di database perpustakaan',
        iconName: 'shield-check',
        unlocked: true,
      },
      {
        id: 'badge-active',
        name: 'Pembaca Aktif',
        description: 'Memiliki riwayat peminjaman buku',
        iconName: 'book-open',
        unlocked: profile.metrics.totalHistory > 0,
      },
    ],
  };
}

export function mapCatalogToBookItem(cat: CatalogItem): BookItem {
  return {
    id: cat.id,
    title: cat.title,
    titleHighlight: cat.title.split(' ')[0] || cat.title,
    titleItalic: cat.title.split(' ').slice(1, 4).join(' '),
    author: cat.author,
    publisher: cat.publisher,
    year: parseInt(cat.publishYear) || 2024,
    isbn: cat.isbn,
    ddcCode: cat.callNumber.split(' ')[0] || '000',
    shelfLocation: `Rak Koleksi Umum (${cat.callNumber})`,
    category: 'Sains & Teknologi',
    formats: ['Fisik'],
    borrowedCount: 12,
    totalPages: 320,
    availableCopies: cat.availableCopies,
    totalCopies: cat.totalCopies || 1,
    rating: 4.8,
    readersCount: 42,
    avgReadTimeMinutes: 280,
    synopsis: cat.synopsis,
    branches: [
      {
        branchId: 'b-pusat',
        branchName: 'Perpustakaan Pusat',
        cityArea: 'Layanan Sirkulasi Lantai 2',
        floorAndShelf: cat.callNumber,
        callNumber: cat.callNumber,
        availableCount: cat.availableCopies,
        totalCount: cat.totalCopies || 1,
        status: cat.availableCopies > 0 ? 'Tersedia' : 'Dipinjam Semua',
      },
    ],
    ebookChapters: [
      {
        chapterNumber: 1,
        title: 'Bab I: Pengantar',
        pagesRange: 'hlm. 1 - 25',
        content: [cat.synopsis],
      },
    ],
    coverGradient: 'from-sky-700 via-indigo-800 to-slate-950',
    coverAccent: '#38bdf8',
    coverPattern: 'geometric',
  };
}

export function mapLoansToCirculation(loans: ActiveLoanItem[], member: Member): CirculationRecord[] {
  return loans.map((loan) => ({
    id: loan.id,
    loanCode: `PINJAM-${loan.barcode}`,
    bookId: loan.bookId,
    bookTitle: loan.title,
    bookAuthor: loan.author,
    ddcCode: loan.callNumber.split(' ')[0] || '000',
    shelfLocation: `Koleksi (${loan.callNumber})`,
    memberId: member.id,
    memberName: member.name,
    memberCode: member.memberCode,
    borrowDate: loan.borrowDate,
    dueDate: loan.dueDate,
    returnDate: null,
    currentPage: 45,
    totalPages: 350,
    readTimeMinutes: 90,
    status: loan.status === 'Overdue' ? 'Terlambat' : 'Dipinjam',
    renewalCount: 0,
    maxRenewals: 2,
    fineAmount: loan.status === 'Overdue' ? Math.abs(loan.daysRemaining) * 1000 : 0,
  }));
}

export function mapFinesToTransactions(fines: FineItem[], member: Member): TransactionRecord[] {
  return fines.map((fine) => {
    const isPaid = typeof fine.Paid === 'object' && fine.Paid !== null && 'data' in fine.Paid
      ? (fine.Paid.data[0] === 1)
      : (fine.Paid === 1 || fine.Paid === true);

    return {
      id: `fine-${fine.fine_id}`,
      invoiceCode: `DND-${String(fine.fine_id).padStart(5, '0')}`,
      memberId: member.id,
      memberName: member.name,
      memberCode: member.memberCode,
      type: 'Denda Keterlambatan',
      description: `${fine.JenisPelanggaran || 'Keterlambatan'} - ${fine.Title || 'Koleksi'}`,
      date: fine.CreateDate ? fine.CreateDate.split(' ')[0] : '2026-11-16',
      amount: parseFloat(fine.JumlahDenda as string) || 0,
      paymentMethod: 'QRIS Perpustakaan',
      status: isPaid ? 'Lunas' : 'Menunggu Pembayaran',
    };
  });
}
