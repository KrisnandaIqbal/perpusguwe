<?= $this->extend('App\Views\layout\main'); ?>

<?= $this->section('style'); ?>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>
    :root {
        --blue-900: #0f2356;
        --blue-800: #1B3878;
        --blue-500: #2d65d4;
        --blue-200: #bdd0f5;
        --blue-100: #dde8fb;
        --blue-50:  #eef3fd;
        --teal-600: #0d8a6a;
        --teal-500: #1fba74;
        --teal-100: #edfaf4;
        --warn-600: #a16207;
        --warn-100: #fefce8;
        --danger:   #e8394d;
        --gray-800: #1e2433;
        --gray-500: #6b7489;
        --gray-300: #d1d7e4;
        --gray-100: #f4f6fb;
        --white:    #ffffff;
        --radius-xl: 20px;
        --radius-lg: 14px;
        --radius-md: 10px;
        --shadow-card: 0 4px 24px rgba(27,56,120,0.10), 0 1px 4px rgba(27,56,120,0.06);
        --shadow-btn:  0 4px 14px rgba(27,56,120,0.30);
    }

    body { font-family: 'Plus Jakarta Sans', 'Segoe UI', sans-serif; }

    /* ── Inner card ── */
    .pm-inner-card {
        background: white; border-radius: var(--radius-xl);
        box-shadow: var(--shadow-card); overflow: hidden; margin-bottom: 18px;
    }
    .pm-inner-header {
        background: linear-gradient(135deg, var(--blue-900), var(--blue-800));
        padding: 16px 24px; display: flex; align-items: center; gap: 10px;
    }
    .pm-inner-header.teal {
        background: linear-gradient(135deg, #064e3b, var(--teal-600));
    }
    .pm-inner-header .h-icon {
        width: 36px; height: 36px; background: rgba(255,255,255,.15);
        border-radius: 9px; display: flex; align-items: center; justify-content: center;
    }
    .pm-inner-header .h-icon i { color: white; font-size: 16px; }
    .pm-inner-header h5 { color: white; font-size: 16px; font-weight: 700; margin: 0; }
    .pm-inner-body { padding: 26px 24px; }

    /* ── Scan center ── */
    .scan-center { text-align: center; padding: 4px 0 20px; }
    .scan-circle {
        width: 70px; height: 70px; border-radius: 50%;
        background: linear-gradient(135deg, var(--blue-100), var(--blue-50));
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 14px;
    }
    .scan-circle i { font-size: 28px; color: var(--blue-800); }
    .scan-center h6 { font-size: 16px; font-weight: 700; color: var(--blue-900); margin-bottom: 5px; }
    .scan-center p  { color: var(--gray-500); font-size: 14px; }

    /* ── Input ── */
    .pm-input-wrap { position: relative; margin-bottom: 18px; }
    .pm-input-wrap .input-icon {
        position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
        color: var(--blue-500); font-size: 16px; pointer-events: none;
    }
    .pm-input {
        width: 100%; padding: 14px 18px 14px 46px;
        border: 2px solid var(--gray-300); border-radius: var(--radius-lg);
        font-size: 18px; font-weight: 700; letter-spacing: 2px;
        color: var(--blue-900); background: var(--gray-100);
        font-family: 'Plus Jakarta Sans', monospace;
        transition: all .25s; outline: none;
    }
    .pm-input::placeholder { letter-spacing: 0; font-weight: 400; color: var(--gray-500); font-size: 14px; }
    .pm-input:focus {
        border-color: var(--blue-500); background: white;
        box-shadow: 0 0 0 4px rgba(45,101,212,.12);
    }

    /* ── Buttons ── */
    .pm-btn {
        display: inline-flex; align-items: center; gap: 7px;
        padding: 12px 24px; border-radius: 50px;
        font-size: 14px; font-weight: 700; font-family: inherit;
        cursor: pointer; border: none; transition: all .2s; text-decoration: none;
    }
    .pm-btn-primary {
        background: linear-gradient(135deg, var(--blue-800), var(--blue-500));
        color: white; box-shadow: var(--shadow-btn);
    }
    .pm-btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(27,56,120,.35); color: white; }
    .pm-btn-teal {
        background: linear-gradient(135deg, #064e3b, var(--teal-600));
        color: white; box-shadow: 0 4px 14px rgba(13,138,106,.35);
    }
    .pm-btn-teal:hover { transform: translateY(-2px); color: white; }
    .pm-btn-ghost {
        background: var(--blue-50); color: var(--blue-800);
        border: 2px solid var(--blue-200);
    }
    .pm-btn-ghost:hover { background: var(--blue-100); color: var(--blue-800); }
    .pm-btn-outline-sm {
        background: transparent; color: var(--blue-800);
        border: 1.5px solid var(--blue-200);
        padding: 7px 14px; font-size: 13px; border-radius: 8px;
        display: inline-flex; align-items: center; gap: 5px; font-weight: 600;
    }
    .pm-btn-outline-sm:hover { background: var(--blue-50); color: var(--blue-800); }

    /* ── Alerts ── */
    .pm-alert {
        border-radius: var(--radius-md); padding: 13px 16px;
        display: flex; align-items: flex-start; gap: 10px;
        font-size: 14px; font-weight: 500; margin-bottom: 14px; border: none;
    }
    .pm-alert i { font-size: 15px; flex-shrink: 0; margin-top: 1px; }
    .pm-alert-danger  { background: #fef1f2; color: var(--danger); }
    .pm-alert-success { background: var(--teal-100); color: var(--teal-600); border: 1px solid #a7f3d0; }
    .pm-alert-warn    { background: var(--warn-100); color: var(--warn-600); border: 1px solid #fde68a; }

    /* ── Book info box ── */
    .book-info-box {
        background: var(--gray-100); border-radius: var(--radius-lg);
        padding: 20px; border-left: 4px solid var(--teal-500);
        margin-bottom: 16px;
    }
    .book-info-box h6 {
        font-size: 14px; font-weight: 700; color: var(--blue-900);
        margin-bottom: 12px; display: flex; align-items: center; gap: 7px;
    }

    /* ── Book item row ── */
    .book-extend-item {
        background: white; border-radius: var(--radius-lg);
        border: 1.5px solid var(--gray-300); padding: 16px 18px;
        margin-bottom: 10px; transition: border-color .2s;
    }
    .book-extend-item:hover { border-color: var(--blue-200); }
    .book-extend-item.selected { border-color: var(--teal-500); background: var(--teal-100); }

    .bei-top {
        display: flex; align-items: flex-start; gap: 12px; margin-bottom: 10px;
    }
    .bei-checkbox-wrap {
        padding-top: 2px; flex-shrink: 0;
    }
    .bei-checkbox {
        width: 20px; height: 20px; accent-color: var(--teal-500); cursor: pointer;
    }
    .bei-title { font-size: 14px; font-weight: 700; color: var(--blue-900); margin-bottom: 3px; }
    .bei-author { font-size: 12px; color: var(--gray-500); margin-bottom: 4px; }
    .bei-barcode { font-size: 12px; color: var(--gray-500); }

    /* ── Due date info ── */
    .due-row {
        display: flex; align-items: center; gap: 10px; flex-wrap: wrap;
        padding-top: 10px; border-top: 1px dashed var(--gray-300);
        font-size: 13px;
    }
    .due-label { color: var(--gray-500); font-weight: 600; min-width: 90px; }
    .due-val   { color: var(--gray-800); font-weight: 700; }
    .due-arrow { color: var(--gray-300); font-size: 16px; }
    .due-new   { color: var(--teal-600); font-weight: 800; }
    .due-new.warn { color: var(--warn-600); }

    /* ── Status badges ── */
    .status-badge {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 3px 10px; border-radius: 50px;
        font-size: 11px; font-weight: 700;
    }
    .status-onloan  { background: var(--blue-50); color: var(--blue-800); }
    .status-overdue { background: #fef1f2; color: var(--danger); }
    .extend-badge {
        display: inline-flex; align-items: center; gap: 4px;
        background: var(--warn-100); color: var(--warn-600);
        padding: 2px 9px; border-radius: 50px; font-size: 11px; font-weight: 700;
    }

    /* ── Summary bar ── */
    .summary-bar {
        background: linear-gradient(135deg, var(--teal-100), #f0fdf4);
        border: 1.5px solid #a7f3d0; border-radius: var(--radius-lg);
        padding: 16px 20px; display: flex; align-items: center;
        justify-content: space-between; gap: 14px; flex-wrap: wrap; margin-top: 16px;
    }
    .summary-bar h6 { font-size: 14px; font-weight: 700; color: var(--teal-600); margin: 0 0 3px; }
    .summary-bar p  { font-size: 12px; color: var(--gray-500); margin: 0; }

    /* ── Loading ── */
    .pm-loading { text-align: center; padding: 24px; }
    .pm-loading i { font-size: 28px; color: var(--blue-500); animation: spin 1s linear infinite; }
    .pm-loading p  { color: var(--gray-500); font-size: 14px; margin-top: 10px; }
    @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }

    /* ── Divider ── */
    .pm-divider { border: none; border-top: 2px dashed var(--gray-300); margin: 20px 0; }
    .pm-divider-top { border-top: 1px dashed var(--gray-300); margin-top: 14px; padding-top: 14px; }

    /* ── History ── */
    .history-header {
        display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px;
    }
    .history-header h6 { font-size: 15px; font-weight: 700; color: var(--blue-900); margin: 0; }
    .history-item {
        background: var(--gray-100); border-radius: var(--radius-md);
        padding: 14px 18px; margin-bottom: 12px;
        border-left: 4px solid var(--teal-500);
        display: flex; align-items: center; justify-content: space-between; gap: 16px;
        transition: all .2s;
    }
    .history-item:hover {
        background: #f8fafc;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    }
    .history-item .hi-title  { font-size: 14px; font-weight: 700; color: var(--blue-900); margin-bottom: 4px; line-height: 1.3; }
    .history-item .hi-meta   { font-size: 12px; color: var(--gray-500); display: flex; flex-wrap: wrap; gap: 12px; align-items: center; }
    .history-item .hi-subinfo{ font-size: 12px; color: var(--gray-500); display: flex; flex-wrap: wrap; gap: 8px; align-items: center; margin-top: 6px; }
    .history-item .hi-date   { font-size: 12px; color: var(--gray-500); white-space: nowrap; text-align: right; }
    .history-empty { text-align: center; padding: 28px; color: var(--gray-500); font-size: 14px; }
    .history-empty i { font-size: 28px; margin-bottom: 10px; display: block; }

    @media (max-width: 768px) {
        .history-item { flex-direction: column; align-items: flex-start; gap: 10px; }
        .history-item .hi-date { text-align: left; }
    }

    /* ── Select all bar ── */
    .select-all-bar {
        display: flex; align-items: center; justify-content: space-between;
        background: var(--blue-50); border-radius: var(--radius-md);
        padding: 10px 16px; margin-bottom: 12px;
        border: 1px solid var(--blue-200);
    }
    .select-all-bar label { font-size: 13px; font-weight: 700; color: var(--blue-800); cursor: pointer; display: flex; align-items: center; gap: 8px; }

    /* ── fade-in ── */
    .fade-in { animation: fadeIn .3s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: none; } }

    @media (max-width: 600px) {
        .pm-inner-body { padding: 18px 14px; }
        .summary-bar { flex-direction: column; }
    }
</style>
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>
<div class="app-main__inner">

    <!-- Page Title -->
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="pe-7s-bookmarks icon-gradient bg-strong-bliss"></i>
                </div>
                <div>Perpanjangan 
                    <div class="page-title-subheading">Sistem Perpanjangan Peminjaman Buku</div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="<?= base_url('sirkulasi-perpanjangan') ?>">
                                <i class="fa fa-home"></i> Home
                            </a>
                        </li>
                        <li class="breadcrumb-item" aria-current="page">Tambah Perpanjangan</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">

            <!-- ── Kartu Scanner ── -->
            <div class="pm-inner-card">
                <div class="pm-inner-header">
                    <div class="h-icon"><i class="fa fa-refresh"></i></div>
                    <h5>Scan Barcode Buku</h5>
                </div>
                <div class="pm-inner-body">

                    <div class="scan-center">
                        <div class="scan-circle"><i class="fa fa-barcode"></i></div>
                        <h6>Scan atau Masukkan Barcode Buku</h6>
                        <p>Arahkan scanner ke barcode salah satu buku yang ingin diperpanjang</p>
                    </div>

                    <div class="pm-input-wrap">
                        <i class="fa fa-barcode input-icon"></i>
                        <input type="text" id="barcodeInput" class="pm-input"
                               placeholder="Masukkan atau scan barcode buku..."
                               autocomplete="off" autofocus>
                    </div>

                    <!-- Extend Days Selector -->
                    <div id="extendSelectorWrap">
                        <p style="text-align:center;font-size:13px;font-weight:700;color:var(--blue-900);margin-bottom:10px;">
                            <i class="fa fa-calendar"></i>&nbsp; Durasi Perpanjangan
                        </p>
                        <p id="extendRuleInfo" style="text-align:center;font-size:13px;color:var(--gray-500);margin:0 0 20px;">
                            Mengikuti aturan jenis anggota peminjam. Scan buku untuk melihat durasinya.
                        </p>
                    </div>

                    <div class="text-center" style="display:flex;justify-content:center;gap:10px;flex-wrap:wrap;">
                        <button type="button" id="checkBookBtn" class="pm-btn pm-btn-ghost">
                            <i class="fa fa-search"></i> Cek Buku
                        </button>
                        <button type="button" id="extendBookBtn" class="pm-btn pm-btn-teal">
                            <i class="fa fa-refresh"></i> Perpanjang Buku
                        </button>
                    </div>

                    <!-- Loading -->
                    <div id="loadingSection" class="pm-loading d-none">
                        <i class="fa fa-spinner"></i>
                        <p>Memproses...</p>
                    </div>

                    <!-- Message -->
                    <div id="messageSection"></div>

                    <!-- Book Info Panel -->
                    <div id="bookInfoSection" class="d-none">
                        <hr class="pm-divider">

                        <!-- Member Info -->
                        <div id="memberInfoBox" class="book-info-box fade-in" style="border-left-color: var(--blue-500);">
                            <h6 id="memberInfoTitle">
                                <i class="fa fa-user-circle"></i> Informasi Anggota
                            </h6>
                        </div>

                        <!-- Select All -->
                        <div class="select-all-bar">
                            <label>
                                <input type="checkbox" id="selectAllCheck" style="width:18px;height:18px;accent-color:var(--teal-500);" checked>
                                Pilih semua buku untuk diperpanjang
                            </label>
                            <span id="selectedCountBadge" class="status-badge status-onloan">0 buku dipilih</span>
                        </div>

                        <!-- Book List -->
                        <div id="bookList"></div>

                        <!-- Summary + Action -->
                        <div id="summaryBar" class="summary-bar d-none">
                            <div>
                                <h6 id="summaryText">Siap diperpanjang</h6>
                                <p id="summarySubtext">Perpanjangan selama <span id="summaryDays">-</span> hari kerja</p>
                            </div>
                            <button type="button" id="confirmExtendBtn" class="pm-btn pm-btn-teal">
                                <i class="fa fa-check"></i> Konfirmasi Perpanjangan
                            </button>
                        </div>

                    </div>

                </div>
            </div>

            <!-- ── Riwayat ── -->
            <div class="pm-inner-card">
                <div class="pm-inner-header teal">
                    <div class="h-icon"><i class="fa fa-history"></i></div>
                    <h5>Riwayat Perpanjangan Terbaru</h5>
                </div>
                <div class="pm-inner-body">

                    <div class="history-header">
                        <h6>5 Perpanjangan Terakhir</h6>
                        <button type="button" id="refreshHistoryBtn" class="pm-btn-outline-sm">
                            <i class="fa fa-sync"></i> Refresh
                        </button>
                    </div>

                    <div id="historyList">
                        <div class="history-empty">
                            <i class="fa fa-clock-o"></i>
                            Klik Refresh untuk melihat riwayat perpanjangan
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>
<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
<script>
class ExtendApp {
    constructor() {
        this.currentMemberId   = null;
        this.currentMemberName = null;
        this.booksData         = [];   // semua buku dari server
        this.extendDays        = 0;    // dari jenis_anggota, dikirim server
        this.maxExtend         = 0;
        window.extendApp       = this;
        this.init();
    }

    init() {
        this.bindEvents();
        this.focusInput();
        this.setupAutoScan();
        this.loadHistory(); // Muat riwayat perpanjangan otomatis saat halaman dibuka
    }

    bindEvents() {
        document.getElementById('checkBookBtn').addEventListener('click', () => this.checkBook());
        document.getElementById('extendBookBtn').addEventListener('click', () => this.extendBook());
        document.getElementById('refreshHistoryBtn').addEventListener('click', () => this.loadHistory());
        document.getElementById('barcodeInput').addEventListener('keypress', e => {
            if (e.key === 'Enter') this.checkBook();
        });
        document.getElementById('selectAllCheck').addEventListener('change', e => {
            this.toggleSelectAll(e.target.checked);
        });
        document.getElementById('confirmExtendBtn').addEventListener('click', () => this.extendBook());
    }

    setupAutoScan() {
        let t;
        document.getElementById('barcodeInput').addEventListener('input', () => {
            clearTimeout(t);
            t = setTimeout(() => {
                if (document.getElementById('barcodeInput').value.length >= 8) this.checkBook();
            }, 500);
        });
    }

    focusInput() { document.getElementById('barcodeInput').focus(); }

    showLoading()   { document.getElementById('loadingSection').classList.remove('d-none'); this.hideMessage(); }
    hideLoading()   { document.getElementById('loadingSection').classList.add('d-none'); }
    hideBookInfo()  { document.getElementById('bookInfoSection').classList.add('d-none'); }
    hideMessage()   { document.getElementById('messageSection').innerHTML = ''; }

    showMessage(message, type = 'success') {
        const map = {
            success: { cls: 'pm-alert-success', icon: 'fa-check-circle' },
            error:   { cls: 'pm-alert-danger',  icon: 'fa-exclamation-circle' },
            warn:    { cls: 'pm-alert-warn',     icon: 'fa-exclamation-triangle' },
        };
        const { cls, icon } = map[type] || map.success;
        document.getElementById('messageSection').innerHTML = `
            <div class="pm-alert ${cls} fade-in mt-3">
                <i class="fa ${icon}"></i><span>${message}</span>
            </div>`;
        if (type === 'success') setTimeout(() => { document.getElementById('messageSection').innerHTML = ''; }, 5000);
    }

    getSelectedIds() {
        return this.booksData
            .filter((_, i) => {
                const cb = document.getElementById('cb_' + i);
                return cb && cb.checked;
            })
            .map(b => b.id);
    }

    toggleSelectAll(checked) {
        this.booksData.forEach((_, i) => {
            const cb = document.getElementById('cb_' + i);
            if (cb && !cb.disabled) cb.checked = checked;
            const item = document.getElementById('bei_' + i);
            if (item) item.classList.toggle('selected', checked);
        });
        this.updateSelectionUI();
    }

    updateSelectionUI() {
        const count   = this.getSelectedIds().length;
        const badge   = document.getElementById('selectedCountBadge');
        const bar     = document.getElementById('summaryBar');
        const sumText = document.getElementById('summaryText');

        const bisaDipilih = this.booksData.filter(b => b.can_extend).length;
        badge.textContent = count + ' buku dipilih';

        if (count > 0) {
            bar.classList.remove('d-none');
            sumText.textContent = count + ' buku siap diperpanjang';
        } else {
            bar.classList.add('d-none');
        }

        // Sync select-all checkbox
        const allCheck = document.getElementById('selectAllCheck');
        allCheck.checked       = count === bisaDipilih && count > 0;
        allCheck.indeterminate = count > 0 && count < bisaDipilih;
        allCheck.disabled      = bisaDipilih === 0;
    }

    formatDate(d) {
        if (!d) return '-';
        const cleanDate = typeof d === 'string' ? d.replace(' ', 'T') : d;
        const date = typeof cleanDate === 'string' ? new Date(cleanDate) : cleanDate;
        if (isNaN(date.getTime())) return d;
        return date.toLocaleDateString('id-ID', {
            day: '2-digit', month: 'short', year: 'numeric'
        });
    }

    renderBookList() {
        const list = document.getElementById('bookList');
        const now  = new Date();

        list.innerHTML = this.booksData.map((book, i) => {
            // Tanggal baru dihitung server (hari kerja), JS cuma menampilkan
            const newDue      = book.new_due_date ? new Date(book.new_due_date) : null;
            const isNewOverdue= newDue && newDue < now; // seharusnya tidak mungkin tapi antisipasi
            const overdueBadge= book.is_overdue
                ? `<span class="status-badge status-overdue"><i class="fa fa-exclamation-circle"></i> Terlambat ${book.days_overdue} hari</span>`
                : `<span class="status-badge status-onloan"><i class="fa fa-clock-o"></i> Sedang Dipinjam</span>`;
            const extendBadge = !book.can_extend
                ? `<span class="extend-badge" style="background:var(--warn-100);color:var(--warn-600);">Batas perpanjangan tercapai (${book.extend_count}/${this.maxExtend})</span>`
                : book.extend_count > 0
                    ? `<span class="extend-badge"><i class="fa fa-refresh"></i> Perpanjang ke-${book.extend_count + 1}</span>`
                    : `<span class="extend-badge" style="background:var(--blue-50);color:var(--blue-500);">Perpanjangan Pertama</span>`;
            const newDueHtml = newDue
                ? `<span class="due-arrow"><i class="fa fa-long-arrow-right"></i></span>
                    <span class="due-new ${isNewOverdue ? 'warn' : ''}">
                        <i class="fa fa-calendar-check-o"></i> ${this.formatDate(newDue)}
                    </span>
                    <span style="font-size:11px;color:var(--teal-600);font-weight:700;">
                        +${this.extendDays} hari kerja
                    </span>`
                : '';

            return `
            <div class="book-extend-item ${book.can_extend ? 'selected' : ''} fade-in" id="bei_${i}">
                <div class="bei-top">
                    <div class="bei-checkbox-wrap">
                        <input type="checkbox" class="bei-checkbox" id="cb_${i}"
                               ${book.can_extend ? 'checked' : 'disabled'} onchange="extendApp.onCheckChange(${i})">
                    </div>
                    <div style="flex:1;">
                        <div class="bei-title">${book.title || '-'}</div>
                        <div class="bei-author">${book.author || '-'}</div>
                        <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;margin-top:4px;">
                            <span class="bei-barcode"><i class="fa fa-barcode"></i> ${book.barcode}</span>
                            ${overdueBadge}
                            ${extendBadge}
                        </div>
                    </div>
                </div>
                <div class="due-row">
                    <span class="due-label"><i class="fa fa-calendar"></i> Jatuh Tempo</span>
                    <span class="due-val">${this.formatDate(book.due_date)}</span>
                    ${newDueHtml}
                </div>
            </div>`;
        }).join('');

        this.updateSelectionUI();
    }

    onCheckChange(i) {
        const cb   = document.getElementById('cb_' + i);
        const item = document.getElementById('bei_' + i);
        if (item) item.classList.toggle('selected', cb.checked);
        this.updateSelectionUI();
    }

    async checkBook() {
        const barcode = document.getElementById('barcodeInput').value.trim();
        if (!barcode) {
            Swal.fire({ icon: 'warning', title: 'Perhatian', text: 'Silakan masukkan barcode buku', confirmButtonColor: '#1B3878' });
            this.focusInput(); return;
        }

        this.showLoading();
        try {
            const res  = await fetch('/sirkulasi-perpanjangan/check-book', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `nomorBarcode=${encodeURIComponent(barcode)}`
            });
            const data = await res.json();

            if (data.status === 'success') {
                this.currentMemberId   = data.data.member_id;
                this.currentMemberName = data.data.member_name;
                this.booksData         = data.data.items;
                this.extendDays        = data.data.extend_days;
                this.maxExtend         = data.data.max_extend;

                document.getElementById('summaryDays').textContent = this.extendDays;
                document.getElementById('extendRuleInfo').textContent = this.maxExtend > 0
                    ? `${this.extendDays} hari kerja, maksimal ${this.maxExtend} kali per buku (aturan jenis anggota).`
                    : 'Jenis anggota peminjam belum diatur untuk perpanjangan. Hubungi admin.';

                // Render member info
                document.getElementById('memberInfoTitle').innerHTML = `
                    <div style="display:flex;flex-direction:column;gap:4px;">
                        <span style="font-size:16px;color:var(--blue-900);">
                            <i class="fa fa-user-circle"></i> ${this.currentMemberName || '-'}
                        </span>
                        <small style="color:var(--gray-500);font-size:13px;font-weight:normal;">
                            <i class="fa fa-book"></i> ${this.booksData.length} buku aktif dalam transaksi ini
                        </small>
                    </div>`;

                this.renderBookList();
                document.getElementById('bookInfoSection').classList.remove('d-none');
                this.hideMessage();
                this.loadHistory();
                this.clearInput();
            } else {
                Swal.fire({ icon: 'error', title: 'Gagal', text: data.message, confirmButtonColor: '#e8394d' });
                this.hideBookInfo();
            }
        } catch (e) {
            Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan saat mengecek buku', confirmButtonColor: '#e8394d' });
        } finally { this.hideLoading(); }
    }

    async extendBook() {
        const selectedIds = this.getSelectedIds();

        if (selectedIds.length === 0) {
            Swal.fire({ icon: 'warning', title: 'Perhatian', text: 'Pilih minimal satu buku untuk diperpanjang', confirmButtonColor: '#1B3878' });
            if (!this.booksData.length) this.focusInput();
            return;
        }

        const result = await Swal.fire({
            icon:              'question',
            title:             'Konfirmasi Perpanjangan',
            html:              `<b>${selectedIds.length} buku</b> akan diperpanjang selama <b>${this.extendDays} hari kerja</b>.<br>Lanjutkan?`,
            showCancelButton:  true,
            confirmButtonColor:'#0d8a6a',
            cancelButtonColor: '#6b7489',
            confirmButtonText: '<i class="fa fa-check"></i> Ya, Perpanjang',
            cancelButtonText:  'Batal'
        });

        if (!result.isConfirmed) return;

        this.showLoading();
        try {
            const res  = await fetch('/sirkulasi-perpanjangan/process-extend', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `item_ids=${encodeURIComponent(JSON.stringify(selectedIds))}`
            });
            const data = await res.json();

            this.hideLoading();

            if (data.status === 'success') {
                Swal.fire({
                    icon:             'success',
                    title:            'Berhasil!',
                    html:             data.message,
                    showCancelButton:  true,
                    confirmButtonColor:'#1fba74',
                    confirmButtonText: '<i class="fa fa-file-text"></i> Lihat Struk',
                    cancelButtonColor: '#0d8a6a',
                    cancelButtonText:  'Tutup',
                    timer:             6000,
                    timerProgressBar:  true
                }).then(result => {
                    if (result.isConfirmed && data.struk_url) {
                        window.location.href = data.struk_url;
                    }
                });

                this.booksData         = [];
                this.currentMemberId   = null;
                this.currentMemberName = null;
                this.hideBookInfo();
                this.loadHistory();
                this.focusInput();
            } else {
                Swal.fire({ icon: 'error', title: 'Gagal', text: data.message, confirmButtonColor: '#e8394d' });
            }
        } catch (e) {
            this.hideLoading();
            Swal.fire({ icon: 'error', title: 'Error Sistem', text: 'Terjadi kesalahan saat memproses perpanjangan', confirmButtonColor: '#e8394d' });
        }
    }

    async loadHistory() {
        try {
            let url = '<?= base_url('sirkulasi-perpanjangan/get-extend-history') ?>?limit=5';
            if (this.currentMemberId) url += '&member_id=' + this.currentMemberId;
            const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } });
            if (!res.ok) throw new Error('HTTP ' + res.status);
            const type = res.headers.get('content-type') || '';
            if (!type.includes('json')) throw new Error('respons bukan JSON, kemungkinan sesi habis atau akses ditolak');
            const data = await res.json();
            if (data.status !== 'success') throw new Error(data.message || 'gagal');
            this.displayHistory(data.data);
        } catch (e) {
            console.error('Error loading history:', e);
            document.getElementById('historyList').innerHTML = '<div class="history-empty"><i class="fa fa-exclamation-triangle"></i> Riwayat gagal dimuat (' + e.message + '). Klik Refresh untuk mencoba lagi.</div>';
        }
    }

    displayHistory(items) {
        const list = document.getElementById('historyList');
        if (!items || !items.length) {
            list.innerHTML = `<div class="history-empty"><i class="fa fa-inbox"></i> Belum ada riwayat perpanjangan</div>`;
            return;
        }
        list.innerHTML = items.map(item => `
            <div class="history-item fade-in">
                <div style="flex: 1; min-width: 0;">
                    <div class="hi-title">${item.Title || 'Judul tidak tersedia'}</div>
                    <div class="hi-meta">
                        <span><i class="fa fa-user" style="color:var(--teal-600);"></i> <strong>${item.Fullname || 'Anggota'}</strong> (${item.MemberNo || '-'})</span>
                        <span><i class="fa fa-barcode"></i> <code>${item.NomorBarcode}</code></span>
                        ${item.Author ? `<span><i class="fa fa-pencil"></i> ${item.Author}</span>` : ''}
                    </div>
                    <div class="hi-subinfo">
                        ${item.OriginalDueDate ? `<span>Jatuh Tempo Semula: <strong>${this.formatDate(item.OriginalDueDate)}</strong></span> <span>&bull;</span>` : ''}
                        <span style="background:#ecfdf5; color:#065f46; padding:2px 8px; border-radius:6px; font-size:11px; font-weight:600;">
                            <i class="fa fa-plus-circle"></i> Tambahan: +10 Hari Kerja
                        </span>
                    </div>
                </div>
                <div class="hi-date">
                    <div style="font-size:11px; color:var(--gray-500); margin-bottom:2px;">Diperpanjang pada:</div>
                    <div style="font-size:12px; font-weight:700; color:var(--blue-900);">${this.formatDate(item.DateExtend)}</div>
                    <div style="margin-top:6px;">
                        <span class="status-badge" style="background:#edfaf4; color:#0a6e43; border:1px solid #bbf7d0;">
                            <i class="fa fa-calendar-check-o"></i> Jatuh Tempo: ${this.formatDate(item.DueDateExtend)}
                        </span>
                    </div>
                </div>
            </div>`).join('');
    }

    clearInput() {
        document.getElementById('barcodeInput').value = '';
        this.focusInput();
    }
}

document.addEventListener('DOMContentLoaded', () => { new ExtendApp(); });
</script>
<?= $this->endSection('script'); ?>