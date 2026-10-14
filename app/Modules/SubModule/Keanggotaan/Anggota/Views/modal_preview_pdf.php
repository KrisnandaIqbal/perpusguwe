<!-- Modal Preview PDF Data Anggota -->
<div class="modal fade" id="modal_preview_pdf" tabindex="-1" role="dialog" aria-labelledby="modal_preview_pdf_title" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document" style="max-width: 92vw;">
        <div class="modal-content" style="height: 90vh; border-radius: 8px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.25);">
            <div class="modal-header py-2 px-3 bg-light border-bottom d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <i class="fa fa-file-pdf text-danger fa-lg mr-2"></i>
                    <div>
                        <h6 class="modal-title font-weight-bold mb-0 text-dark" id="modal_preview_pdf_title">
                            Pratinjau Dokumen PDF Data Anggota
                        </h6>
                        <small class="text-muted">Format resmi disesuaikan dengan data pendaftaran online</small>
                    </div>
                </div>
                <button type="button" class="close ml-0 p-2" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" style="outline: none;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <div class="modal-body p-0 position-relative" style="height: calc(90vh - 120px); overflow-y: auto; background-color: #525659;">
                <!-- Loading State (Gunakan inline style flex tanpa d-flex agar jQuery hide() bekerja sempurna) -->
                <div id="pdf_preview_loading" style="display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: calc(90vh - 120px); color: #ffffff; background-color: #525659; padding: 40px 20px;">
                    <div class="spinner-border text-light mb-3" role="status" style="width: 3rem; height: 3rem;">
                        <span class="sr-only">Memuat...</span>
                    </div>
                    <h6 class="font-weight-bold mb-1">Menyiapkan Pratinjau Dokumen...</h6>
                    <p class="small text-white-50 mb-0">Sedang menyusun data <span id="preview_loading_count">0</span> anggota terpilih</p>
                </div>
                
                <!-- Dokumen Viewer Container -->
                <div id="pdf_preview_content" style="display: none; padding: 24px 10px; min-height: 100%;"></div>
            </div>
            
            <div class="modal-footer py-2 px-3 bg-light border-top d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    <i class="fa fa-check-circle text-success mr-1"></i> Terpilih: <strong id="preview_count_badge">0</strong> anggota
                </div>
                <div>
                    <button type="button" class="btn btn-outline-secondary mr-2" id="btn_open_tab_pdf" title="Buka di tab baru" disabled>
                        <i class="fa fa-external-link-alt mr-1"></i> Buka di Tab Baru
                    </button>
                    <button type="button" class="btn btn-secondary mr-2" data-dismiss="modal" data-bs-dismiss="modal">
                        Tutup
                    </button>
                    <button type="button" class="btn btn-primary" id="btn_download_from_preview" title="Unduh file PDF ke komputer" disabled>
                        <i class="fa fa-download mr-1"></i> Unduh PDF
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
