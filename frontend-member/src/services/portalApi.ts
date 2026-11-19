export interface MemberProfile {
  id: string;
  memberNo: string;
  name: string;
  email: string;
  phone: string;
  address: string;
  registerDate: string;
  expiryDate: string;
  institution: string;
  status: string;
  photoUrl: string | null;
  metrics: {
    activeLoans: number;
    overdueLoans: number;
    unpaidFines: number;
    totalHistory: number;
  };
}

export interface ActiveLoanItem {
  id: string;
  loanId: string;
  bookId: string;
  barcode: string;
  title: string;
  author: string;
  publisher: string;
  publishYear: string;
  callNumber: string;
  coverUrl: string | null;
  borrowDate: string;
  dueDate: string;
  status: 'Active' | 'Overdue';
  daysRemaining: number;
}

export interface CatalogItem {
  id: string;
  title: string;
  author: string;
  publisher: string;
  publishYear: string;
  isbn: string;
  callNumber: string;
  subject: string;
  synopsis: string;
  coverUrl: string | null;
  totalCopies: number;
  availableCopies: number;
  status: 'Available' | 'Borrowed';
}

export interface LoanHistoryItem {
  loan_item_id: number;
  LoanDate: string;
  DueDate: string;
  ActualReturn: string | null;
  LoanStatus: string;
  LateDays: number | null;
  Title: string;
  Author: string;
  NomorBarcode: string;
}

export interface FineItem {
  fine_id: number;
  JumlahDenda: string | number;
  Paid: { data: number[] } | number | boolean;
  CreateDate: string;
  Title: string;
  JenisPelanggaran: string;
}

export const api = {
  async getProfile(memberNo?: string): Promise<MemberProfile> {
    const url = memberNo ? `/api/member/portal/profile?member_no=${memberNo}` : '/api/member/portal/profile';
    const res = await fetch(url);
    const json = await res.json();
    if (!json.success) throw new Error(json.message || 'Gagal memuat profil');
    return json.data;
  },

  async getLoans(memberNo?: string): Promise<ActiveLoanItem[]> {
    const url = memberNo ? `/api/member/portal/loans?member_no=${memberNo}` : '/api/member/portal/loans';
    const res = await fetch(url);
    const json = await res.json();
    if (!json.success) throw new Error(json.message || 'Gagal memuat pinjaman');
    return json.data;
  },

  async getCatalogs(searchQuery: string = '', limit: number = 20): Promise<CatalogItem[]> {
    const q = encodeURIComponent(searchQuery);
    const res = await fetch(`/api/member/portal/catalogs?q=${q}&limit=${limit}`);
    const json = await res.json();
    if (!json.success) throw new Error(json.message || 'Gagal memuat katalog');
    return json.data;
  },

  async getHistory(memberNo?: string): Promise<{ loans: LoanHistoryItem[]; fines: FineItem[] }> {
    const url = memberNo ? `/api/member/portal/history?member_no=${memberNo}` : '/api/member/portal/history';
    const res = await fetch(url);
    const json = await res.json();
    if (!json.success) throw new Error(json.message || 'Gagal memuat riwayat');
    return json.data;
  },

  async extendLoan(loanItemId: string): Promise<{ success: boolean; message: string; newDueDate: string }> {
    const res = await fetch('/api/member/portal/extend', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: new URLSearchParams({ loan_item_id: loanItemId }),
    });
    const json = await res.json();
    if (!json.success) throw new Error(json.message || 'Gagal memperpanjang buku');
    return json;
  },
};
