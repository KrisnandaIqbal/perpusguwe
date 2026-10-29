<?= $this->extend('App\Views\layout\main'); ?>
<?= $this->section('style'); ?>
<style>
.summary-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
    margin-bottom: 20px;
}
.summary-box {
    background: #ffffff;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 16px 20px;
}
.summary-box .summary-label {
    font-size: 13px;
    font-weight: 600;
    color: #495057;
    margin-bottom: 6px;
}
.summary-box .summary-value {
    font-size: 24px;
    font-weight: 700;
    color: #1f2937;
    line-height: 1.2;
}
.summary-box.summary-success .summary-value {
    color: #15803d;
}
.summary-box.summary-failed .summary-value {
    color: #b91c1c;
}
.summary-box.summary-rate .summary-value {
    color: #1e40af;
}
.columns-section {
    background-color: #ffffff;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
}
.filters-container {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 15px;
    margin-bottom: 20px;
}
.filter-section {
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 18px 20px;
    background-color: #f8f9fa;
}
.filter-section h6 {
    color: #344767;
    font-weight: 600;
    margin-bottom: 12px;
}
.preview-container {
    margin-top: 24px;
    border-top: 1px solid #dee2e6;
    padding-top: 20px;
}
.preview-table {
    max-height: 520px;
    overflow-y: auto;
}
.status-badge {
    display: inline-block;
    padding: 4px 10px;
    font-size: 12px;
    font-weight: 600;
    border-radius: 4px;
}
.status-badge-success {
    background-color: #dcfce7;
    color: #166534;
}
.status-badge-failed {
    background-color: #fee2e2;
    color: #991b1b;
}
@media (max-width: 992px) {
    .summary-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    .filters-container {
        grid-template-columns: 1fr;
    }
}
@media (max-width: 576px) {
    .summary-grid {
        grid-template-columns: 1fr;
    }
}
</style>
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>
<div class="app-main__inner">
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="pe-7s-mail-open-file icon-gradient bg-strong-bliss"></i>
                </div>
                <div>Laporan Pengiriman Email
                    <div class="page-title-subheading">Riwayat dan Ekspor Pengiriman Notifikasi Email Sistem</div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i> Home</a></li>
                        <li class="breadcrumb-item">Laporan</li>
                        <li class="breadcrumb-item active" aria-current="page">Laporan Pengiriman Email</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="summary-grid" id="summaryGrid">
        <div class="summary-box">
            <div class="summary-label">Total Pengiriman</div>
            <div class="summary-value" id="statTotal"><?= number_format($summary['total'] ?? 0) ?></div>
        </div>
        <div class="summary-box summary-success">
            <div class="summary-label">Berhasil Terkirim</div>
            <div class="summary-value" id="statSuccess"><?= number_format($summary['success'] ?? 0) ?></div>
        </div>
        <div class="summary-box summary-failed">
            <div class="summary-label">Gagal Terkirim</div>
            <div class="summary-value" id="statFailed"><?= number_format($summary['failed'] ?? 0) ?></div>
        </div>
        <div class="summary-box summary-rate">
            <div class="summary-label">Rasio Keberhasilan</div>
            <div class="summary-value" id="statRate"><?= esc($summary['rate'] ?? 0) ?>%</div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5><strong>Filter dan Ekspor Laporan Pengiriman Email</strong></h5>
            <p class="text-muted mb-0">Pilih kolom dan kriteria pencarian untuk menampilkan pratinjau atau mengunduh laporan.</p>
        </div>
        <div class="card-body">
            <?php if (session('error')) : ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i> <?= esc(session('error')) ?>
                </div>
            <?php endif ?>

            <form id="filterForm">
                <?= csrf_field() ?>

                <div class="columns-section">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0"><i class="fas fa-columns"></i> Pilih Kolom Laporan</h6>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="select_all_columns">
                            <label class="form-check-label font-weight-bold text-primary" for="select_all_columns">
                                Pilih Semua Kolom
                            </label>
                        </div>
                    </div>
                    <div class="row">
                        <?php
                        $defaultChecked = ['sent_at', 'recipient_email', 'recipient_name', 'member_no', 'email_type', 'subject', 'status'];
                        foreach ($columnNames as $colKey => $colLabel) :
                            $checked = in_array($colKey, $defaultChecked, true) ? 'checked' : '';
                        ?>
                            <div class="col-md-4 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input column-checkbox" type="checkbox" name="columns[]" value="<?= esc($colKey) ?>" id="col_<?= esc($colKey) ?>" <?= $checked ?>>
                                    <label class="form-check-label" for="col_<?= esc($colKey) ?>">
                                        <?= esc($colLabel) ?>
                                    </label>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="filters-container">
                    <div class="filter-section">
                        <h6><i class="fas fa-calendar-alt"></i> Periode Waktu Pengiriman</h6>
                        <div class="row">
                            <div class="col-md-6 mb-2 mb-md-0">
                                <label for="start_date">Tanggal Mulai</label>
                                <input type="date" name="start_date" id="start_date" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label for="end_date">Tanggal Akhir</label>
                                <input type="date" name="end_date" id="end_date" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="filter-section">
                        <h6><i class="fas fa-tag"></i> Kategori Notifikasi Email</h6>
                        <label for="email_type">Jenis Email</label>
                        <select name="email_type" id="email_type" class="form-control">
                            <option value="">Semua Jenis Notifikasi</option>
                            <?php foreach ($emailTypes as $typeKey => $typeLabel) : ?>
                                <option value="<?= esc($typeKey) ?>"><?= esc($typeLabel) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="filter-section">
                        <h6><i class="fas fa-check-circle"></i> Status Pengiriman</h6>
                        <label for="status">Status</label>
                        <select name="status" id="status" class="form-control">
                            <option value="">Semua Status</option>
                            <option value="success">Berhasil Terkirim</option>
                            <option value="failed">Gagal Terkirim</option>
                        </select>
                    </div>

                    <div class="filter-section">
                        <h6><i class="fas fa-search"></i> Pencarian Penerima / Subjek</h6>
                        <label for="keyword">Kata Kunci</label>
                        <input type="text" name="keyword" id="keyword" class="form-control" placeholder="Cari email, nama anggota, nomor anggota, atau subjek...">
                    </div>
                </div>

                <div class="d-flex justify-content-center flex-wrap mb-4">
                    <button type="button" class="btn btn-primary btn-lg px-4 m-1" id="btnPreview">
                        <i class="fas fa-eye"></i> Tampilkan Pratinjau
                    </button>
                    <button type="button" class="btn btn-success btn-lg px-4 m-1" id="btnExport">
                        <i class="fas fa-file-excel"></i> Ekspor ke Excel
                    </button>
                    <button type="button" class="btn btn-danger btn-lg px-4 m-1" id="btnExportPdf">
                        <i class="fas fa-file-pdf"></i> Ekspor ke PDF
                    </button>
                    <button type="button" class="btn btn-secondary btn-lg px-4 m-1" id="btnResetFilter">
                        <i class="fas fa-eraser"></i> Atur Ulang Filter
                    </button>
                </div>
            </form>

            <div class="preview-container" id="previewSection">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0"><i class="fas fa-table"></i> Data Riwayat Pengiriman Email</h5>
                    <span class="text-muted small" id="previewMeta">Menampilkan hingga 100 baris terbaru</span>
                </div>
                <div class="preview-table" id="previewTableContainer">
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-spinner fa-spin"></i> Memuat data riwayat pengiriman email...
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
<script>
$(document).ready(function() {
    const columnNames = <?= json_encode($columnNames) ?>;

    function updateSelectAllStatus() {
        const total = $('.column-checkbox').length;
        const checked = $('.column-checkbox:checked').length;

        if (checked === 0) {
            $('#select_all_columns').prop('indeterminate', false).prop('checked', false);
        } else if (checked === total) {
            $('#select_all_columns').prop('indeterminate', false).prop('checked', true);
        } else {
            $('#select_all_columns').prop('indeterminate', true);
        }
    }

    function getSelectedColumns() {
        const cols = [];
        $('.column-checkbox:checked').each(function() {
            cols.push($(this).val());
        });
        return cols;
    }

    function escapeHtml(value) {
        return $('<div>').text(value == null ? '-' : String(value)).html();
    }

    function updateSummaryCards(summary) {
        if (!summary) return;
        $('#statTotal').text(Number(summary.total || 0).toLocaleString('id-ID'));
        $('#statSuccess').text(Number(summary.success || 0).toLocaleString('id-ID'));
        $('#statFailed').text(Number(summary.failed || 0).toLocaleString('id-ID'));
        $('#statRate').text((summary.rate || 0) + '%');
    }

    function renderPreviewTable(data, columns, total) {
        $('#previewMeta').text('Menampilkan ' + total + ' baris data (maksimal 100 baris pada pratinjau)');

        if (!data || data.length === 0) {
            $('#previewTableContainer').html(
                '<div class="alert alert-warning mb-0">' +
                '<i class="fas fa-info-circle"></i> Tidak ada riwayat pengiriman email yang sesuai dengan filter saat ini.' +
                '</div>'
            );
            return;
        }

        let html = '<div class="table-responsive"><table class="table table-bordered table-striped table-hover mb-0">';
        html += '<thead class="thead-dark"><tr><th style="width:50px;">No</th>';
        columns.forEach(function(col) {
            html += '<th>' + escapeHtml(columnNames[col] || col) + '</th>';
        });
        html += '</tr></thead><tbody>';

        data.forEach(function(row, index) {
            html += '<tr><td>' + (index + 1) + '</td>';
            columns.forEach(function(col) {
                const val = row[col] ?? '-';
                if (col === 'status') {
                    const badgeClass = val === 'Berhasil' ? 'status-badge-success' : 'status-badge-failed';
                    html += '<td><span class="status-badge ' + badgeClass + '">' + escapeHtml(val) + '</span></td>';
                } else {
                    html += '<td>' + escapeHtml(val) + '</td>';
                }
            });
            html += '</tr>';
        });

        html += '</tbody></table></div>';
        $('#previewTableContainer').html(html);
    }

    function loadPreview() {
        const selectedColumns = getSelectedColumns();
        if (selectedColumns.length === 0) {
            $('#previewTableContainer').html(
                '<div class="alert alert-warning mb-0">Pilih minimal satu kolom laporan untuk menampilkan data.</div>'
            );
            return;
        }

        const $btn = $('#btnPreview');
        $btn.html('<i class="fas fa-spinner fa-spin"></i> Memuat...').prop('disabled', true);
        $('#previewTableContainer').html(
            '<div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x text-primary"></i><p class="mt-2 mb-0">Memuat data riwayat pengiriman email...</p></div>'
        );

        $.ajax({
            url: '<?= base_url('laporan-email/preview') ?>',
            type: 'POST',
            data: {
                columns: selectedColumns,
                start_date: $('#start_date').val(),
                end_date: $('#end_date').val(),
                email_type: $('#email_type').val(),
                status: $('#status').val(),
                keyword: $('#keyword').val()
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    updateSummaryCards(response.summary);
                    renderPreviewTable(response.data, response.columns, response.total);
                } else {
                    $('#previewTableContainer').html(
                        '<div class="alert alert-danger mb-0">' + escapeHtml(response.message || 'Gagal memuat data.') + '</div>'
                    );
                }
            },
            error: function() {
                $('#previewTableContainer').html(
                    '<div class="alert alert-danger mb-0">Terjadi kesalahan saat menghubungi server. Silakan coba lagi.</div>'
                );
            },
            complete: function() {
                $btn.html('<i class="fas fa-eye"></i> Tampilkan Pratinjau').prop('disabled', false);
            }
        });
    }

    function submitExportForm(actionUrl) {
        const selectedColumns = getSelectedColumns();
        if (selectedColumns.length === 0) {
            alert('Pilih minimal satu kolom untuk diekspor.');
            return;
        }

        const form = $('<form>', {
            method: 'POST',
            action: actionUrl
        });

        form.append($('<input>', {
            type: 'hidden',
            name: '<?= csrf_token() ?>',
            value: '<?= csrf_hash() ?>'
        }));

        selectedColumns.forEach(function(col) {
            form.append($('<input>', {
                type: 'hidden',
                name: 'columns[]',
                value: col
            }));
        });

        ['start_date', 'end_date', 'email_type', 'status', 'keyword'].forEach(function(field) {
            form.append($('<input>', {
                type: 'hidden',
                name: field,
                value: $('#' + field).val()
            }));
        });

        $('body').append(form);
        form.submit();
        form.remove();
    }

    $('#select_all_columns').on('change', function() {
        $('.column-checkbox').prop('checked', $(this).is(':checked'));
        updateSelectAllStatus();
    });

    $('.column-checkbox').on('change', function() {
        updateSelectAllStatus();
    });

    $('#btnPreview').on('click', function() {
        loadPreview();
    });

    $('#btnExport').on('click', function() {
        submitExportForm('<?= base_url('laporan-email/export') ?>');
    });

    $('#btnExportPdf').on('click', function() {
        submitExportForm('<?= base_url('laporan-email/export_pdf') ?>');
    });

    $('#btnResetFilter').on('click', function() {
        $('#start_date').val('');
        $('#end_date').val('');
        $('#email_type').val('');
        $('#status').val('');
        $('#keyword').val('');
        loadPreview();
    });

    $('#keyword').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            loadPreview();
        }
    });

    updateSelectAllStatus();
    loadPreview();
});
</script>
<?= $this->endSection('script'); ?>
