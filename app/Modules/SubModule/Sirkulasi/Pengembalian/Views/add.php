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
        --success:  #1fba74;
        --danger:   #e8394d;
        --warn-bg:  #fffbe6;
        --warn-col: #856404;
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
    .pm-btn-success {
        background: linear-gradient(135deg, #0b9e5f, var(--success));
        color: white; box-shadow: 0 4px 14px rgba(31,186,116,.35);
    }
    .pm-btn-success:hover { transform: translateY(-2px); color: white; }
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

    /* ── Messages ── */
    .pm-alert {
        border-radius: var(--radius-md); padding: 13px 16px;
        display: flex; align-items: flex-start; gap: 10px;
        font-size: 14px; font-weight: 500; margin-bottom: 14px; border: none;
    }
    .pm-alert i { font-size: 15px; flex-shrink: 0; margin-top: 1px; }
    .pm-alert-danger  { background: #fef1f2; color: var(--danger); }
    .pm-alert-success { background: var(--blue-50); color: var(--blue-800); border: 1px solid var(--blue-200); }

    /* ── Book info box ── */
    .book-info-box {
        background: var(--gray-100); border-radius: var(--radius-lg);
        padding: 20px; border-left: 4px solid var(--blue-500);
        margin-bottom: 16px;
    }
    .book-info-box h6 {
        font-size: 14px; font-weight: 700; color: var(--blue-900);
        margin-bottom: 12px; display: flex; align-items: center; gap: 7px;
    }
    .book-detail {
        display: flex; justify-content: space-between; align-items: center;
        padding: 7px 0; border-bottom: 1px solid var(--gray-300);
        font-size: 13px;
    }
    .book-detail:last-child { border-bottom: none; }
    .book-detail .bd-label { color: var(--gray-500); font-weight: 500; }
    .book-detail .bd-val   { color: var(--gray-800); font-weight: 600; text-align: right; }

    /* ── Status badges ── */
    .status-badge {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 4px 12px; border-radius: 50px;
        font-size: 12px; font-weight: 700;
    }
    .status-available { background: #edfaf4; color: #0a6e43; }
    .status-loaned    { background: var(--warn-bg); color: var(--warn-col); }
    .status-overdue   { background: #fef1f2; color: var(--danger); }

    /* ── Loading ── */
    .pm-loading {
        text-align: center; padding: 24px;
    }
    .pm-loading i { font-size: 28px; color: var(--blue-500); animation: spin 1s linear infinite; }
    .pm-loading p  { color: var(--gray-500); font-size: 14px; margin-top: 10px; }
    @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }

    /* ── Divider ── */
    .pm-divider { border: none; border-top: 2px dashed var(--gray-300); margin: 22px 0; }

    /* ── History items ── */
    .history-header {
        display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px;
    }
    .history-header h6 { font-size: 15px; font-weight: 700; color: var(--blue-900); margin: 0; }

    .history-item {
        background: var(--gray-100); border-radius: var(--radius-md);
        padding: 14px 18px; margin-bottom: 12px;
        border-left: 4px solid var(--blue-500);
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

    @media (max-width: 768px) {
        .history-item { flex-direction: column; align-items: flex-start; gap: 10px; }
        .history-item .hi-date { text-align: left; }
    }

    .history-empty {
        text-align: center; padding: 28px;
        color: var(--gray-500); font-size: 14px;
    }

	/* ── Tambahan untuk Divider Antar Buku ── */
    .pm-divider-top {
        border-top: 1px dashed var(--gray-300);
        margin-top: 15px;
        padding-top: 15px;
    }
    .history-empty i { font-size: 28px; margin-bottom: 10px; display: block; }

    /* ── fade-in ── */
    .fade-in { animation: fadeIn .3s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: none; } }

    /* ── Modal Styling: Hilangkan shadow dialog & backdrop ── */
    #modalPelanggaran {
        z-index: 1060 !important;
        background: transparent !important;
    }
    #modalPelanggaran.show {
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
    }
    #modalPelanggaran .modal-dialog {
        max-width: 520px;
        width: 92%;
        margin: auto !important;
        box-shadow: none !important;
        background: transparent !important;
        border: none !important;
    }
    #modalPelanggaran .modal-dialog::before,
    #modalPelanggaran .modal-dialog::after {
        display: none !important;
        content: none !important;
        height: 0 !important;
    }
    #modalPelanggaran .modal-content {
        border-radius: var(--radius-xl) !important;
        border: 1px solid var(--gray-300) !important;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12) !important;
        overflow: hidden;
        background: #ffffff !important;
    }
    .modal-backdrop,
    .modal-backdrop.fade,
    .modal-backdrop.show {
        display: none !important;
        opacity: 0 !important;
        visibility: hidden !important;
        backdrop-filter: none !important;
        -webkit-backdrop-filter: none !important;
    }
    #modalPelanggaran .close:hover {
        opacity: 1 !important;
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
                    <i class="pe-7s-refresh-2 icon-gradient bg-strong-bliss"></i>
                </div>
                <div>Tambah Pengembalian
                    <div class="page-title-subheading">Sistem Pengembalian Buku </div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="<?= base_url('sirkulasi-pengembalian/create') ?>"><i class="fa fa-home"></i> Home</a>
                        </li>
                        <li class="breadcrumb-item" aria-current="page">Pengembalian Mandiri</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">

            <!-- Main Card: Scanner -->
            <div class="pm-inner-card">
                <div class="pm-inner-header">
                    <div class="h-icon"><i class="fa fa-undo"></i></div>
                    <h5>Scan Barcode Buku</h5>
                </div>
                <div class="pm-inner-body">

                    <div class="scan-center">
                        <div class="scan-circle"><i class="fa fa-barcode"></i></div>
                        <h6>Scan atau Masukkan Barcode Buku</h6>
                        <p>Arahkan scanner ke barcode buku yang ingin dikembalikan</p>
                    </div>

                    <div class="pm-input-wrap">
                        <i class="fa fa-barcode input-icon"></i>
                        <input type="text" id="barcodeInput" class="pm-input"
                               placeholder="Masukkan atau scan barcode buku..."
                               autocomplete="off" autofocus>
                    </div>
                     
                    <div class="pm-input-wrap" style="margin-bottom: 22px;">
                        <h6>Tanggal Pengembalian</h6>
                        <i class="fa fa-calendar input-icon"></i>
                        <input type="date" id="returnDateInput" class="pm-input"
                               value="<?= date('Y-m-d') ?>" title="Tanggal Pengembalian">
                    </div>

                    <div class="text-center" style="display:flex;justify-content:center;gap:10px;flex-wrap:wrap">
                        <button type="button" id="checkBookBtn" class="pm-btn pm-btn-ghost">
                            <i class="fa fa-search"></i> Cek Buku
                        </button>
                        <button type="button" id="returnBookBtn" class="pm-btn pm-btn-success">
                            <i class="fa fa-undo"></i> Kembalikan Buku
                        </button>
                    </div>

                    <!-- Loading -->
                    <div id="loadingSection" class="pm-loading d-none">
                        <i class="fa fa-spinner"></i>
                        <p>Memproses...</p>
                    </div>

                    <!-- Message -->
                    <div id="messageSection"></div>

                    <!-- Book Info -->
                    <div id="bookInfoSection" class="d-none">
                        <div class="book-info-box fade-in">
                            <h6><i class="fa fa-book"></i> Informasi Buku</h6>
                            <div id="bookDetails"></div>
                        </div>
                    </div>



                </div>
            </div>

            <!-- History Card -->
            <div class="pm-inner-card">
                <div class="pm-inner-header">
                    <div class="h-icon"><i class="fa fa-history"></i></div>
                    <h5>Riwayat Pengembalian Terbaru</h5>
                </div>
                <div class="pm-inner-body">

                    <div class="history-header">
                        <h6>5 Pengembalian Terakhir</h6>
                        <button type="button" id="refreshHistoryBtn" class="pm-btn-outline-sm">
                            <i class="fa fa-sync"></i> Refresh
                        </button>
                    </div>

                    <div id="historyList">
                        <div class="history-empty">
                            <i class="fa fa-clock-o"></i>
                            Klik Refresh untuk melihat riwayat pengembalian
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>

    <!-- Modal Pelanggaran -->
    <div class="modal fade" id="modalPelanggaran" data-backdrop="false" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content" style="border-radius: var(--radius-xl); border: none; overflow: hidden;">

                <!-- Header -->
                <div class="modal-header" style="background: linear-gradient(135deg, #7c1d1d, var(--danger)); border: none; padding: 16px 24px;">
                    <div style="display:flex; align-items:center; gap:10px;">
                        <div style="width:36px;height:36px;background:rgba(255,255,255,.15);border-radius:9px;display:flex;align-items:center;justify-content:center;">
                            <i class="fa fa-exclamation-triangle" style="color:white;font-size:16px;"></i>
                        </div>
                        <h5 class="modal-title" style="color:white;font-weight:700;margin:0;">Proses Pelanggaran Keterlambatan</h5>
                    </div>
                    <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" style="background:transparent;border:none;font-size:24px;line-height:1;color:white;opacity:0.85;cursor:pointer;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body" style="padding: 24px;">

                    <!-- Info terlambat -->
                    <div class="pm-alert pm-alert-danger mb-3" id="overdueInfoBox">
                        <i class="fa fa-clock-o"></i>
                        <span id="overdueInfoText">Terdapat buku yang melewati tanggal jatuh tempo.</span>
                    </div>

                    <!-- Daftar buku terlambat -->
                    <div id="overdueBookList" style="margin-bottom: 18px;"></div>

                    <hr class="pm-divider" style="margin: 16px 0;">

                    <!-- Form Pelanggaran -->
                    <div id="violationFormSection">
                        <div class="mb-3">
                            <label style="font-size:13px;font-weight:700;color:var(--blue-900);margin-bottom:6px;display:block;">
                                <i class="fa fa-list-ul"></i> Jenis Pelanggaran
                            </label>
                            <select id="jenisPelanggaranSelect" class="form-select" style="border-radius:var(--radius-md);font-size:14px;border:2px solid var(--gray-300);">
                                <option value="">-- Pilih Jenis Pelanggaran --</option>
                            </select>
                        </div>

                        <!-- Informasi Suspend Pengganti Denda -->
                        <div style="background:#fef2f2;border:1px solid #fee2e2;border-radius:var(--radius-md);padding:14px 16px;color:#991b1b;font-size:13px;line-height:1.5;">
                            <i class="fa fa-ban" style="margin-right:6px;"></i>
                            <strong>Ketentuan Sanksi:</strong> Perpustakaan tidak menerapkan denda uang. Keterlambatan N hari otomatis dikenakan sanksi suspend peminjaman selama N hari terhitung sejak tanggal pengembalian.
                        </div>

                        <!-- Hidden Denda Fields (Perpustakaan tidak memakai denda) -->
                        <div style="display:none;">
                            <select id="jenisDendaSelect"><option value="5" selected>Suspend</option></select>
                            <input type="number" id="jumlahDendaInput" value="0">
                            <input type="checkbox" id="perHariCheck">
                            <small id="dendaPreview"></small>
                        </div>
                    </div>
                </div>

                <div class="modal-footer" style="border:none;padding:12px 24px 20px;gap:10px;">
                    <button type="button" class="pm-btn pm-btn-ghost" data-dismiss="modal" data-bs-dismiss="modal" style="flex:1;">
                        <i class="fa fa-times"></i> Batal
                    </button>
                    <button type="button" id="confirmViolationBtn" class="pm-btn pm-btn-primary" style="flex:2;background:linear-gradient(135deg,#7c1d1d,var(--danger));box-shadow:0 4px 14px rgba(232,57,77,.35);">
                        <i class="fa fa-gavel"></i> Proses Pengembalian & Suspend
                    </button>
                </div>

            </div>
        </div>
    </div>

</div>

<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
<script>
class SelfReturnApp {
    constructor() {
        this.currentMemberId   = null;
        this.currentMemberName = null;
        this.booksToReturn     = [];
        this.jenisPelanggaran  = [];
        this.jenisDenda        = [];
        window.selfReturnApp   = this;
        this.init();
    }

    init() {
        // Pindahkan modal ke document.body agar tidak tertutup backdrop akibat stacking context .app-container
        const modalEl = document.getElementById('modalPelanggaran');
        if (modalEl && modalEl.parentNode !== document.body) {
            document.body.appendChild(modalEl);
        }
        this.bindEvents();
        this.focusInput();
        this.setupAutoScan();
        this.loadMasterData(); // Muat dropdown sekali saja
        this.loadHistory(); // Muat riwayat pengembalian otomatis saat halaman dibuka
    }

    bindEvents() {
        document.getElementById('checkBookBtn').addEventListener('click', () => this.checkBook());
        document.getElementById('returnBookBtn').addEventListener('click', () => this.returnBook());
        document.getElementById('refreshHistoryBtn').addEventListener('click', () => this.loadHistory());
        document.getElementById('barcodeInput').addEventListener('keypress', (e) => {
            if (e.key === 'Enter') this.checkBook();
        });
        document.getElementById('returnDateInput').addEventListener('change', () => {
            if (this.booksToReturn.length > 0) {
                const firstBarcode = this.booksToReturn[0].barcode;
                if (firstBarcode) {
                    document.getElementById('barcodeInput').value = firstBarcode;
                    this.checkBook();
                }
            }
        });

        // Tombol di dalam modal
        const modalEl = document.getElementById('modalPelanggaran');
        if (modalEl) {
            modalEl.querySelectorAll('[data-dismiss="modal"], [data-bs-dismiss="modal"]').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    this.closeViolationModal();
                });
            });
        }
        const skipBtn = document.getElementById('skipViolationBtn');
        if (skipBtn) {
            skipBtn.addEventListener('click', () => {
                this.closeViolationModal();
                this.submitReturn(false, null);
            });
        }
        const confirmBtn = document.getElementById('confirmViolationBtn');
        if (confirmBtn) {
            confirmBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.handleConfirmViolation();
            });
        }

        // Live preview kalkulasi denda (jika elemen tersedia)
        const dendaInput = document.getElementById('jumlahDendaInput');
        if (dendaInput) dendaInput.addEventListener('input', () => this.updateDendaPreview());
        const perHari = document.getElementById('perHariCheck');
        if (perHari) perHari.addEventListener('change', () => this.updateDendaPreview());
    }

    setupAutoScan() {
        let scanTimeout;
        const input = document.getElementById('barcodeInput');
        input.addEventListener('input', () => {
            clearTimeout(scanTimeout);
            scanTimeout = setTimeout(() => {
                if (input.value.length >= 8) this.checkBook();
            }, 500);
        });
    }

    async loadMasterData() {
        try {
            const [resP, resD] = await Promise.all([
                fetch('<?= base_url('sirkulasi-pengembalian/get-jenis-pelanggaran') ?>').then(r => r.json()),
                fetch('<?= base_url('sirkulasi-pengembalian/get-jenis-denda') ?>').then(r => r.json())
            ]);
            if (resP.status === 'success') {
                this.jenisPelanggaran = resP.data;
                const sel = document.getElementById('jenisPelanggaranSelect');
                resP.data.forEach(p => {
                    sel.innerHTML += `<option value="${p.ID}">${p.JenisPelanggaran}</option>`;
                });
            }
            if (resD.status === 'success') {
                this.jenisDenda = resD.data;
                const sel = document.getElementById('jenisDendaSelect');
                resD.data.forEach(d => {
                    sel.innerHTML += `<option value="${d.ID}">${d.Name}</option>`;
                });
            }
        } catch (e) { console.error('Gagal memuat master data:', e); }
    }

    focusInput()    { document.getElementById('barcodeInput').focus(); }
    showLoading()   { document.getElementById('loadingSection').classList.remove('d-none'); this.hideMessage(); this.hideBookInfo(); }
    hideLoading()   { document.getElementById('loadingSection').classList.add('d-none'); }
    hideBookInfo()  { document.getElementById('bookInfoSection').classList.add('d-none'); }
    hideMessage()   { document.getElementById('messageSection').innerHTML = ''; }

    showMessage(message, type = 'success') {
        const sec = document.getElementById('messageSection');
        const cls  = type === 'success' ? 'pm-alert-success' : 'pm-alert-danger';
        const icon = type === 'success' ? 'fa fa-check-circle' : 'fa fa-exclamation-circle';
        sec.innerHTML = `<div class="pm-alert ${cls} fade-in mt-3"><i class="${icon}"></i><span>${message}</span></div>`;
        if (type === 'success') setTimeout(() => { sec.innerHTML = ''; }, 5000);
    }

    renderBookList() {
        const sec     = document.getElementById('bookInfoSection');
        const details = document.getElementById('bookDetails');
        const titleSec = sec.querySelector('h6');

        if (this.booksToReturn.length === 0) { this.hideBookInfo(); return; }

        titleSec.innerHTML = `
            <div style="display:flex;flex-direction:column;gap:5px;">
                <span style="color:var(--blue-900);font-size:16px;"><i class="fa fa-user-circle"></i> ${this.currentMemberName || '-'}</span>
                <small style="color:var(--gray-500);font-size:13px;font-weight:normal;">
                    <i class="fa fa-book"></i> ${this.booksToReturn.length} buku siap dikembalikan
                </small>
            </div>`;

        let itemsHtml = '';
        this.booksToReturn.forEach((book, index) => {
            const isReallyOverdue = Boolean(book.is_overdue && Number(book.days_overdue) > 0);
            const statusBadge = isReallyOverdue
                ? `<span class="status-badge status-overdue"><i class="fa fa-exclamation-circle"></i> Terlambat ${book.days_overdue} hari</span>`
                : `<span class="status-badge status-available"><i class="fa fa-clock-o"></i> Sedang Dipinjam</span>`;

            itemsHtml += `
                <div class="${index > 0 ? 'pm-divider-top' : ''} position-relative fade-in" style="padding-right:45px;">
                    <button type="button" class="btn btn-sm btn-outline-danger position-absolute"
                            style="right:0;top:10px;border-radius:8px;width:35px;height:35px;"
                            onclick="selfReturnApp.removeBookItem('${book.id}')">
                        <i class="fa fa-trash"></i>
                    </button>
                    <div class="book-detail">
                        <span class="bd-label">Judul</span>
                        <span class="bd-val" style="text-align:right;max-width:75%;">${book.title || '-'}</span>
                    </div>
                    <div class="book-detail">
                        <span class="bd-label">Barcode</span>
                        <span class="bd-val">${book.barcode}</span>
                    </div>
                    <div class="book-detail">
                        <span class="bd-label">Jatuh Tempo</span>
                        <span class="bd-val">${this.formatDate(book.due_date)}</span>
                    </div>
                    <div class="book-detail">
                        <span class="bd-label">Status</span>
                        <span class="bd-val">${statusBadge}</span>
                    </div>
                </div>`;
        });

        details.innerHTML = itemsHtml;
        sec.classList.remove('d-none');
    }

    removeBookItem(id) {
        this.booksToReturn = this.booksToReturn.filter(b => String(b.id) !== String(id));
        this.renderBookList();
    }

    async checkBook() {
        const barcode = document.getElementById('barcodeInput').value.trim();
        const returnDate = document.getElementById('returnDateInput').value;
        if (!barcode) {
            Swal.fire({ icon: 'warning', title: 'Perhatian', text: 'Silakan masukkan barcode buku', confirmButtonColor: '#1B3878' });
            this.focusInput(); return;
        }
        this.showLoading();
        try {
            const res  = await fetch('<?= base_url('sirkulasi-pengembalian/check-book') ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `nomorBarcode=${encodeURIComponent(barcode)}&returnDate=${encodeURIComponent(returnDate)}`
            });
            const data = await res.json();
            if (data.status === 'success') {
                this.currentMemberId   = data.data.member_id;
                this.currentMemberName = data.data.member_name;
                this.booksToReturn     = data.data.items;
                this.renderBookList();
                this.hideMessage();
                this.loadHistory();
                this.clearInput();
            } else {
                Swal.fire({ icon: 'error', title: 'Gagal', text: data.message, confirmButtonColor: '#e8394d' });
                this.hideBookInfo();
            }
        } catch (e) {
            Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan saat mengecek buku', confirmButtonColor: '#e8394d' });
            this.hideBookInfo();
        } finally { this.hideLoading(); }
    }

    returnBook() {
        if (this.booksToReturn.length === 0) {
            Swal.fire({ icon: 'warning', title: 'Perhatian', text: 'Tidak ada buku yang dipilih. Silakan scan buku terlebih dahulu.', confirmButtonColor: '#1B3878' });
            this.focusInput(); return;
        }

        // Cek apakah ada buku yang benar-benar terlambat (> 0 hari)
        const overdueBooks = this.booksToReturn.filter(b => b.is_overdue && Number(b.days_overdue) > 0);

        if (overdueBooks.length > 0) {
            this.showViolationModal(overdueBooks);
        } else {
            // Tidak ada keterlambatan, langsung proses
            this.submitReturn(false, null);
        }
    }

    openViolationModal() {
        const modalEl = document.getElementById('modalPelanggaran');
        if (!modalEl) return;

        if (modalEl.parentNode !== document.body) {
            document.body.appendChild(modalEl);
        }

        if (window.jQuery && typeof window.jQuery(modalEl).modal === 'function') {
            window.jQuery(modalEl).modal({
                backdrop: false,
                show: true
            });
            window.jQuery(modalEl).modal('show');
            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
            return;
        }
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            if (typeof bootstrap.Modal.getOrCreateInstance === 'function') {
                bootstrap.Modal.getOrCreateInstance(modalEl).show();
                return;
            } else if (typeof bootstrap.Modal === 'function') {
                new bootstrap.Modal(modalEl).show();
                return;
            }
        }
        modalEl.classList.add('show');
        modalEl.style.display = 'block';
        modalEl.removeAttribute('aria-hidden');
        modalEl.setAttribute('aria-modal', 'true');
        document.body.classList.add('modal-open');
    }

    closeViolationModal() {
        const modalEl = document.getElementById('modalPelanggaran');
        if (!modalEl) return;

        try {
            if (window.jQuery && typeof window.jQuery(modalEl).modal === 'function') {
                window.jQuery(modalEl).modal('hide');
            } else if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                if (typeof bootstrap.Modal.getInstance === 'function') {
                    const inst = bootstrap.Modal.getInstance(modalEl);
                    if (inst) {
                        inst.hide();
                    }
                } else if (typeof bootstrap.Modal.getOrCreateInstance === 'function') {
                    const inst = bootstrap.Modal.getOrCreateInstance(modalEl);
                    if (inst) {
                        inst.hide();
                    }
                }
            }
        } catch (e) {
            console.warn('Error closing modal:', e);
        }

        setTimeout(() => {
            modalEl.classList.remove('show');
            modalEl.style.display = 'none';
            modalEl.setAttribute('aria-hidden', 'true');
            modalEl.removeAttribute('aria-modal');
            document.body.classList.remove('modal-open');
            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
        }, 150);
    }

    showViolationModal(overdueBooks) {
        // Render daftar buku terlambat di modal
        const listEl = document.getElementById('overdueBookList');
        listEl.innerHTML = overdueBooks.map(b => `
            <div style="background:var(--gray-100);border-radius:var(--radius-md);padding:10px 14px;margin-bottom:8px;border-left:4px solid var(--danger);">
                <div style="font-size:14px;font-weight:700;color:var(--gray-800);">${b.title || '-'}</div>
                <div style="font-size:12px;color:var(--gray-500);margin-top:4px;">
                    <i class="fa fa-barcode"></i> ${b.barcode} &nbsp;|&nbsp;
                    <span style="color:var(--danger);font-weight:700;"><i class="fa fa-exclamation-circle"></i> Terlambat ${b.days_overdue} hari</span> &nbsp;|&nbsp;
                    <span style="color:#b91c1c;font-weight:700;"><i class="fa fa-ban"></i> Sanksi Suspend: ${b.days_overdue} hari</span>
                </div>
            </div>`).join('');

        document.getElementById('overdueInfoText').textContent =
            `${overdueBooks.length} dari ${this.booksToReturn.length} buku melewati tanggal jatuh tempo.`;

        // Simpan referensi overdueBooks untuk kalkulasi preview
        this._overdueBooks = overdueBooks;

        // Auto-select "Terlambat" / "Keterlambatan" jika opsi belum dipilih secara manual
        const sel = document.getElementById('jenisPelanggaranSelect');
        if (sel && sel.options.length > 0) {
            let found = false;
            for (let i = 0; i < sel.options.length; i++) {
                const optText = (sel.options[i].text || '').toLowerCase();
                if (optText.includes('terlambat') || optText.includes('lambat')) {
                    sel.selectedIndex = i;
                    found = true;
                    break;
                }
            }
            if (!found && (!sel.value || sel.value === '')) {
                if (sel.options.length > 1) {
                    sel.selectedIndex = 1;
                }
            }
        }

        this.openViolationModal();
    }

    updateDendaPreview() {
        // Ditiadakan karena perpustakaan tidak memakai denda uang
    }

    handleConfirmViolation() {
        const confirmBtn = document.getElementById('confirmViolationBtn');
        if (confirmBtn) {
            confirmBtn.disabled = true;
            confirmBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Memproses...';
        }

        const jenisPelanggaranSelect = document.getElementById('jenisPelanggaranSelect');
        const jenisPelanggaranId     = (jenisPelanggaranSelect && jenisPelanggaranSelect.value) ? jenisPelanggaranSelect.value : 1;

        this.closeViolationModal();

        this.submitReturn(true, {
            jenis_pelanggaran_id: jenisPelanggaranId,
            jenis_denda_id:       5, // Suspend
            jumlah_denda:         0
        });
    }

    async submitReturn(processViolation, violationData) {
        this.showLoading();

        const itemIds = this.booksToReturn.map(b => b.id);
        const returnDate = document.getElementById('returnDateInput').value;

        const body = [
            `item_ids=${encodeURIComponent(JSON.stringify(itemIds))}`,
            `process_violation=${processViolation ? '1' : '0'}`,
            `violation_data=${encodeURIComponent(JSON.stringify(violationData || {}))}`,
            `returnDate=${encodeURIComponent(returnDate)}`
        ].join('&');

        const resetConfirmBtn = () => {
            const confirmBtn = document.getElementById('confirmViolationBtn');
            if (confirmBtn) {
                confirmBtn.disabled = false;
                confirmBtn.innerHTML = '<i class="fa fa-gavel"></i> Proses Pengembalian & Suspend';
            }
        };

        try {
            const res  = await fetch('<?= base_url('sirkulasi-pengembalian/process-return') ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body
            });
            const data = await res.json();

            this.hideLoading();
            resetConfirmBtn();

            if (data.status === 'success') {
                window.location.href = data.struk_url;
            } else {
                this.renderBookList();
                Swal.fire({ icon: 'error', title: 'Gagal', text: data.message, confirmButtonColor: '#e8394d' });
            }
        } catch (e) {
            this.hideLoading();
            this.renderBookList();
            resetConfirmBtn();
            Swal.fire({ icon: 'error', title: 'Error Sistem', text: 'Terjadi kesalahan saat memproses pengembalian', confirmButtonColor: '#e8394d' });
        }
    }

    async loadHistory() {
        try {
            let url = '<?= base_url('sirkulasi-pengembalian/getReturnHistory') ?>?limit=5';
            if (this.currentMemberId) url += '&member_id=' + this.currentMemberId;
            const res  = await fetch(url);
            const data = await res.json();
            if (data.status === 'success') this.displayHistory(data.data);
        } catch (e) { console.error('Error loading history:', e); }
    }

    displayHistory(items) {
        const list = document.getElementById('historyList');
        if (!items || !items.length) {
            list.innerHTML = `<div class="history-empty"><i class="fa fa-inbox"></i> Belum ada riwayat pengembalian</div>`;
            return;
        }
        list.innerHTML = items.map(item => {
            const isReallyOverdue = (parseInt(item.LateDays, 10) > 0);
            const statusBadge = isReallyOverdue
                ? `<span class="status-badge status-overdue"><i class="fa fa-exclamation-circle"></i> Terlambat ${item.LateDays} hari</span>`
                : `<span class="status-badge status-available"><i class="fa fa-check-circle"></i> Tepat Waktu</span>`;

            const durationDays = item.duration_days !== undefined ? item.duration_days : '-';

            return `
            <div class="history-item fade-in">
                <div style="flex: 1; min-width: 0;">
                    <div class="hi-title">${item.Title || 'Judul tidak tersedia'}</div>
                    <div class="hi-meta">
                        <span><i class="fa fa-user" style="color:var(--blue-500);"></i> <strong>${item.Fullname || 'Anggota'}</strong> (${item.MemberNo || '-'})</span>
                        <span><i class="fa fa-barcode"></i> <code>${item.NomorBarcode}</code></span>
                        ${item.Author ? `<span><i class="fa fa-pencil"></i> ${item.Author}</span>` : ''}
                    </div>
                    <div class="hi-subinfo">
                        <span><i class="fa fa-calendar-check-o"></i> Pinjam: <strong>${this.formatDate(item.LoanDate)}</strong></span>
                        <span>&bull;</span>
                        <span>Jatuh Tempo: <strong>${this.formatDate(item.DueDate)}</strong></span>
                        <span>&bull;</span>
                        <span style="background:#eef2ff; color:#3730a3; padding:2px 8px; border-radius:6px; font-size:11px; font-weight:600;">
                            <i class="fa fa-clock-o"></i> Durasi Pinjam: ${durationDays} hari
                        </span>
                    </div>
                </div>
                <div class="hi-date">
                    <div style="font-size:11px; color:var(--gray-500); margin-bottom:2px;">Tgl Dikembalikan:</div>
                    <div style="font-size:12px; font-weight:700; color:var(--blue-900);">${this.formatDate(item.ActualReturn)}</div>
                    <div style="margin-top:6px;">${statusBadge}</div>
                </div>
            </div>`;
        }).join('');
    }

    formatDate(dateString) {
        if (!dateString) return '-';
        const cleanDate = dateString.replace(' ', 'T');
        const d = new Date(cleanDate);
        if (isNaN(d.getTime())) return dateString;
        return d.toLocaleDateString('id-ID', {
            year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit'
        });
    }

    clearInput() {
        document.getElementById('barcodeInput').value = '';
        this.focusInput();
    }
}

document.addEventListener('DOMContentLoaded', () => { new SelfReturnApp(); });
</script>
<?= $this->endSection('script'); ?>