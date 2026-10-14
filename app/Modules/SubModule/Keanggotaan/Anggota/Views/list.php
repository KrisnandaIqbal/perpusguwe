<?php
$regionModel = new \Region\Models\RegionModel();
$request = service('request');
?>

<?= $this->extend('App\Views\layout\main'); ?>

<?= $this->section('style'); ?>
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>
<div class="app-main__inner">
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="pe-7s-id icon-gradient bg-strong-bliss"></i>
                </div>
                <div>Anggota
                    <div class="page-title-subheading">Daftar semua Anggota
                    </div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav class="" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i></a></li>
                        <li class="breadcrumb-item">Keanggotaan</li>
                        <li class="breadcrumb-item ">Daftar Anggota</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="main-card mb-3 card">
        <div class="card-header"><i class="header-icon lnr-list icon-gradient bg-plum-plate"> </i>Tabel Anggota
            <div class="btn-actions-pane-right actions-icon-btn">
                <a href="<?= base_url('anggota/create') ?>" class=" btn btn-success" title=""><i class="fa fa-plus"></i>
                    Tambah Anggota
                </a>
            </div>
        </div>

        <div class="card-body">
            <form name="form_items" id="form_items">
                
                <div class="d-block mb-3 pb-3 border-bottom">
                    <button type="button" id="proses_keranjang" class="btn btn-warning" data-toggle="tooltip" data-placement="top" title="Semua anggota yang terpilih"><i class="fa fa-shopping-cart"></i> Pindahkan ke Keranjang</button>
                    <button type="button" id="print_kartu" class="btn btn-primary ml-2" data-toggle="tooltip" data-placement="top" title="Print kartu anggota yang terpilih"><i class="fa fa-print"></i> Print Kartu</button>
                    <button type="button" id="aktifkan_online" class="btn btn-success ml-2" data-toggle="tooltip" data-placement="top" title="Aktifkan akses online untuk anggota yang terpilih"><i class="fa fa-globe"></i> Aktifkan Online</button>
                    <button type="button" id="btn_export_pdf" class="btn btn-secondary ml-2" data-toggle="tooltip" data-placement="top" title="Pilih anggota dahulu" disabled><i class="fa fa-file-pdf"></i> Pratinjau & Ekspor PDF (<span id="count_export_pdf">0</span>)</button>
                    <button type="button" id="btn_export_xlsx" class="btn btn-outline-success ml-2" data-toggle="tooltip" data-placement="top" title="Ekspor seluruh data anggota ke Excel"><i class="fa fa-file-excel"></i> Ekspor Semua XLSX</button>
                    <a href="javascript:void(0)" id="clear_selection" class="small text-muted ml-2" style="display:none; text-decoration: underline;">Kosongkan pilihan</a>
                </div>

                <table style="width: 100%;" id="tbl_data" class="table table-hover table-striped table-bordered">
                    <thead>
                        <tr>
                            <th class="text-center" width="35">No</th>
                            <th class="text-center" width="35">
                                <input type="checkbox" class="check_data" title="Pilih Semua">
                            </th>
                            <th>Nama Anggota</th>
                            <th>No. Anggota</th>
                            <th width="100">Tgl. Register</th>
                            <th width="100">Tgl. Berakhir</th>
                            <th width="130">Status Anggota</th>
                            <th style="min-width: 150px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </form>
        </div>
        </div>
</div>
<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
<?= $this->include('Anggota\Views\modal_upload'); ?>
<?= $this->include('Anggota\Views\modal_camera'); ?>
<?= $this->include('Anggota\Views\modal_approval'); ?>
<?= $this->include('Anggota\Views\modal_preview_pdf'); ?>
<script>
      $(document).ready(function() {
        <?php if (session()->getFlashdata('swal_icon')) : ?>
            Swal.fire({
                icon: '<?= session()->getFlashdata('swal_icon') ?>', // gunakan 'icon' jika SweetAlert2 versi terbaru
                title: '<?= session()->getFlashdata('swal_title') ?>',
                html: '<?= session()->getFlashdata('swal_html') ? session()->getFlashdata('swal_html') : session()->getFlashdata('swal_text') ?>',
                showConfirmButton: false,
                timer: 3000
            });
        <?php endif; ?>
    });
</script>
<script>
    // Pilihan anggota lintas halaman (FR-01, FR-02, FR-03, FR-04)
    var selectedMemberIds = new Set();

    function updateExportPdfState() {
        var count = selectedMemberIds ? selectedMemberIds.size : 0;
        $('#count_export_pdf').text(count);
        if (count > 0) {
            $('#btn_export_pdf').prop('disabled', false).removeAttr('disabled').attr('title', 'Pratinjau & Ekspor ' + count + ' anggota ke PDF');
            $('#clear_selection').show();
        } else {
            $('#btn_export_pdf').prop('disabled', true).attr('disabled', 'disabled').attr('title', 'Pilih anggota dahulu');
            $('#clear_selection').hide();
        }
    }

    function syncCheckboxesOnDraw() {
        if (!selectedMemberIds) return;
        var allChecked = true;
        var hasRows = false;
        $('#tbl_data tbody input[type="checkbox"]').each(function() {
            hasRows = true;
            var val = $(this).val();
            if (val && selectedMemberIds.has(String(val))) {
                $(this).prop('checked', true);
            } else {
                $(this).prop('checked', false);
                allChecked = false;
            }
        });
        $('.check_data').prop('checked', hasRows && allChecked);
        updateExportPdfState();
    }

    var t;
    $(document).ready(function() {
        t = $('#tbl_data').DataTable({
            "processing": true,
            "serverSide": true,
            "scrollX": true,
            "scrollCollapse": true,
            "ajax": {
                "url": '<?php echo site_url('api/anggota/datatable'); ?>',
            },
            
            // KONFIGURASI DOM BARU
            // l = length menu (atas kiri), f = filter/search (atas kanan)
            // t = table (tengah)
            // i = info (bawah kiri), p = pagination (bawah kanan)
          "dom": "<'row mb-2'<'col-md-6 col-sm-12 text-left'l><'col-md-6 col-sm-12 text-right'f>>" +
       "<'row'<'col-md-12'tr>>" +
       "<'row mt-2'<'col-md-5 col-sm-12 text-left'i><'col-md-7 col-sm-12 d-flex justify-content-end'p>>",
            
            "pagingType": "full_numbers",
            "oLanguage": {
                "sSearch": "<i class='fa fa-search'></i> _INPUT_",
                "sLengthMenu": "_MENU_",
                "oPaginate": {
                    "sNext": "<i class='fa fa-chevron-right'></i>",
                    "sPrevious": "<i class='fa fa-chevron-left'></i>",
                    "sLast": "<i class='fa fa-chevron-double-right'></i>",
                    "sFirst": "<i class='fa fa-chevron-double-left'></i>",
                }
            },
            "columns": [{
                    data: 'no',
                    className: 'text-center'
                }, {
                    data: 'cid',
                    className: 'text-center',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'FullName'
                },
                {
                    data: 'MemberNo'
                },
                {
                    data: 'RegisterDate',
                    className: 'text-center'
                },
                {
                    data: 'EndDate',
                    className: 'text-center'
                },
                {
                    data: 'StatusAnggota',
                    className: 'text-center'
                },
                {
                    data: 'action',
                    className: 'text-center',
                    searchable: false,
                    orderable: false
                },
                {
                    data: 'CreateDate',
                    visible: false,
                }

            ],
            "order": [
                [8, "desc"]
            ],
            "drawCallback": function(data, type, full, meta) {
                var api = this.api();
                var data = api.rows().data();

                $('.apply-status').bootstrapToggle();

                $(".apply-status").on('change', function() {
                    var url = $(this).attr('data-href');
                    var field = $(this).attr('data-field');
                    var value = $(this).is(':checked') == true ? 1 : 0;
                    var data_post = 'field=' + field + '&value=' + value;

                    ajax_post(url, data_post);
                });

                $(".apply-select").on('change', function() {
                    var approveValue = $(this).attr('data-approve-value');
                    if (approveValue && $(this).val() == approveValue) {
                        open_approval(this);
                        return;
                    }
                    if (approveValue && $(this).val() == $(this).attr('data-reject-value')) {
                        open_approval(this, true);
                        return;
                    }

                    var url = $(this).attr('data-href');
                    var field = $(this).attr('data-field');
                    var value = $(this).val();
                    var data_post = 'field=' + field + '&value=' + value;

                    ajax_post(url, data_post);
                });

                $.each(data, function(i, row) {
                    $("#lazy" + row.id).Lazy();
                });

                $('.image-link').magnificPopup({
                    type: 'image'
                });

                syncCheckboxesOnDraw();
            },
            "initComplete": function(settings, json) {

            }
        });
    });

    // Centang / hapus centang seluruh baris di halaman aktif
    $(document).on('click change', '.check_data', function() {
        var isChecked = $(this).is(':checked');
        $('.check_data').prop('checked', isChecked);
        $('#tbl_data tbody input[type="checkbox"]').each(function() {
            var val = $(this).val();
            if (val && val !== 'on') {
                $(this).prop('checked', isChecked);
                if (isChecked) {
                    selectedMemberIds.add(String(val));
                } else {
                    selectedMemberIds.delete(String(val));
                }
            }
        });
        updateExportPdfState();
    });

    // Centang / hapus centang checkbox individual per baris
    $(document).on('click change', '#tbl_data tbody input[type="checkbox"], input.check', function() {
        var val = $(this).val();
        if (val && val !== 'on') {
            if ($(this).is(':checked')) {
                selectedMemberIds.add(String(val));
            } else {
                selectedMemberIds.delete(String(val));
            }
        }

        var allChecked = true;
        var rowBoxes = $('#tbl_data tbody input[type="checkbox"]');
        if (rowBoxes.length === 0) {
            allChecked = false;
        } else {
            rowBoxes.each(function() {
                if (!$(this).is(':checked')) {
                    allChecked = false;
                    return false;
                }
            });
        }
        $('.check_data').prop('checked', allChecked);
        updateExportPdfState();
    });

    // Kosongkan pilihan
    $('#clear_selection').click(function(e) {
        e.preventDefault();
        selectedMemberIds.clear();
        $('#tbl_data input[type="checkbox"]').prop('checked', false);
        $('.check_data').prop('checked', false);
        updateExportPdfState();
    });

    // Fungsi membuka pratinjau dokumen di Modal (Murni HTML langsung di DIV modal, instan, bebas blokir & tanpa auto-download)
    function openPdfPreview() {
        // Fallback: Jika set kosong, periksa checkbox aktif di tabel
        if (selectedMemberIds.size === 0) {
            $('#tbl_data tbody input[type="checkbox"]:checked, input.check:checked').each(function() {
                var val = $(this).val();
                if (val && val !== 'on') {
                    selectedMemberIds.add(String(val));
                }
            });
        }

        var count = selectedMemberIds.size;
        $('#preview_count_badge').text(count);
        $('#preview_loading_count').text(count);

        // Kunci tombol aksi sampai dokumen selesai dirender
        $('#btn_download_from_preview').prop('disabled', true).html('<i class="fa fa-download mr-1"></i> Unduh PDF');
        $('#btn_open_tab_pdf').prop('disabled', true);

        // Buka modal pratinjau
        $('#modal_preview_pdf').modal('show');

        // Reset tampilan spinner
        $('#pdf_preview_loading').html(`
            <div class="spinner-border text-light mb-3" role="status" style="width: 3rem; height: 3rem;">
                <span class="sr-only">Memuat...</span>
            </div>
            <h6 class="font-weight-bold mb-1">Menyiapkan Pratinjau Dokumen...</h6>
            <p class="small text-white-50 mb-0">Sedang menyusun data ${count} anggota terpilih</p>
        `).css('display', 'flex');
        $('#pdf_preview_content').hide().html('');

        var ids = Array.from(selectedMemberIds);

        // Ambil HTML dokumen via AJAX POST (bebas auto-download & langsung tampil)
        $.ajax({
            url: '<?= base_url('anggota/export_pdf') ?>',
            type: 'POST',
            data: {
                preview: 1,
                member_ids: ids
                <?php if (csrf_token()): ?>,
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                <?php endif; ?>
            },
            dataType: 'html',
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            },
            success: function(html) {
                // Sembunyikan loading spinner seutuhnya & tampilkan lembar dokumen
                $('#pdf_preview_loading').css('display', 'none').hide();
                $('#pdf_preview_content').html(html).show();

                // Pratinjau sudah terlihat, sekarang aktifkan tombol Unduh PDF & Buka di Tab Baru
                $('#btn_download_from_preview').prop('disabled', false);
                $('#btn_open_tab_pdf').prop('disabled', false);
            },
            error: function(xhr, status, error) {
                console.error('Preview Error:', xhr.status, error, xhr.responseText);
                var errMsg = 'Terjadi kendala saat memuat pratinjau data';
                if (xhr.status === 400 || xhr.status === 403) {
                    errMsg = xhr.responseText ? $('<div>' + xhr.responseText + '</div>').text() : errMsg;
                }
                $('#pdf_preview_loading').html(`
                    <div class="text-center p-4">
                        <i class="fa fa-exclamation-triangle fa-2x text-warning mb-3"></i>
                        <h6 class="font-weight-bold mb-2">Gagal memuat pratinjau dokumen</h6>
                        <p class="small text-white-50 mb-3">${errMsg}</p>
                        <div>
                            <button type="button" class="btn btn-outline-light btn-sm" id="btn_retry_preview">
                                <i class="fa fa-redo mr-1"></i> Coba Lagi
                            </button>
                        </div>
                    </div>
                `).show();
            }
        });
    }

    // Handler klik Pratinjau & Ekspor PDF dari toolbar daftar anggota
    $('#btn_export_pdf').click(function() {
        if (selectedMemberIds.size === 0) {
            Swal.fire({
                title: 'Peringatan',
                text: 'Silakan pilih minimal 1 anggota untuk melihat pratinjau PDF',
                icon: 'warning',
                confirmButtonText: 'OK'
            });
            return false;
        }
        if (selectedMemberIds.size > 50) {
            Swal.fire({
                title: 'Batas Maksimal Ekspor',
                text: 'Maksimal 50 anggota per dokumen PDF. Anda telah memilih ' + selectedMemberIds.size + ' anggota.',
                icon: 'warning',
                confirmButtonText: 'OK'
            });
            return false;
        }

        // Tampilkan pratinjau terlebih dahulu (tanpa men-download file)
        openPdfPreview();
    });

    // Handler tombol "Unduh PDF" dari dalam modal pratinjau
    // HANYA dipicu saat tombol ini diklik setelah pratinjau terlihat
    $('#btn_download_from_preview').click(function() {
        if ($(this).prop('disabled') || selectedMemberIds.size === 0) {
            return false;
        }

        var $btn = $(this);
        var originalHtml = $btn.html();
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i> Menyiapkan File PDF...');

        // Trigger pengunduhan file .pdf melalui form submit POST
        var form = $('<form></form>');
        form.attr('method', 'post');
        form.attr('action', '<?= base_url('anggota/export_pdf') ?>');

        <?php if (csrf_token()): ?>
            form.append('<input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" />');
        <?php endif; ?>

        form.append('<input type="hidden" name="preview" value="0" />');

        selectedMemberIds.forEach(function(id) {
            form.append('<input type="hidden" name="member_ids[]" value="' + id + '" />');
        });

        $('body').append(form);
        form.submit();
        form.remove();

        setTimeout(function() {
            $btn.prop('disabled', false).html(originalHtml);
        }, 3500);
    });

    // Handler tombol "Buka di Tab Baru" dari dalam modal pratinjau
    $('#btn_open_tab_pdf').click(function() {
        if ($(this).prop('disabled') || selectedMemberIds.size === 0) return;

        var form = $('<form></form>');
        form.attr('method', 'post');
        form.attr('target', '_blank');
        form.attr('action', '<?= base_url('anggota/export_pdf') ?>');

        <?php if (csrf_token()): ?>
            form.append('<input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" />');
        <?php endif; ?>

        form.append('<input type="hidden" name="preview" value="1" />');

        selectedMemberIds.forEach(function(id) {
            form.append('<input type="hidden" name="member_ids[]" value="' + id + '" />');
        });

        $('body').append(form);
        form.submit();
        form.remove();
    });

    $(document).on('click', '#btn_retry_preview', function() {
        openPdfPreview();
    });

    // Reset konten & status modal saat modal ditutup
    $('#modal_preview_pdf').on('hidden.bs.modal', function () {
        $('#pdf_preview_content').hide().html('');
        $('#pdf_preview_loading').css('display', 'flex');
        $('#btn_download_from_preview').prop('disabled', true);
        $('#btn_open_tab_pdf').prop('disabled', true);
    });

    // Handler klik Ekspor Semua XLSX (FR-10, FR-15)
    $('#btn_export_xlsx').click(function() {
        var $btn = $(this);
        var originalHtml = $btn.html();
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Memproses...');

        window.location.href = '<?= base_url('anggota/export_xlsx') ?>';

        setTimeout(function() {
            $btn.prop('disabled', false).html(originalHtml);
        }, 3000);
    });

    $('#proses_keranjang').click(function() {
        var form = $('#form_items');
        var serialize_bulk = form.serialize();
        var url = "<?= base_url('anggota/proses_keranjang') ?>" + '?' + serialize_bulk;
        console.log(serialize_bulk);
        console.log(url);

        Swal.fire({
            title: 'Anda yakin?',
            html: "Semua anggota yang terpilih akan dipindahkan ke keranjang",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#dd6b55',
            confirmButtonText: '<?= lang('App.btn.yes') ?>',
            cancelButtonText: '<?= lang('App.btn.no') ?>'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
        return false;
    });

    // Event handler untuk tombol Print Kartu
    $('#print_kartu').click(function() {
        // Debug: cek semua checkbox yang ada
        console.log('All checkboxes in table:', $('#tbl_data input[type="checkbox"]'));

        // Coba beberapa selector berbeda untuk menangkap checkbox
        var checkedBoxes = $('#tbl_data input[type="checkbox"]:checked').not('.check_data');

        console.log('Checked boxes found:', checkedBoxes.length);
        console.log('Checked boxes:', checkedBoxes);

        if (checkedBoxes.length === 0) {
            Swal.fire({
                title: 'Peringatan',
                text: 'Silakan pilih minimal satu anggota untuk dicetak kartunya',
                icon: 'warning',
                showConfirmButton: true,
                confirmButtonText: 'OK'
            });
            return false;
        }

        // Collect all selected IDs
        var selectedIds = [];
        checkedBoxes.each(function() {
            var checkboxValue = $(this).val();
            var checkboxName = $(this).attr('name');
            var checkboxId = $(this).attr('id');

            console.log('Checkbox - Value:', checkboxValue, 'Name:', checkboxName, 'ID:', checkboxId);

            if (checkboxValue && checkboxValue !== 'on') {
                selectedIds.push(checkboxValue);
            }
        });

        console.log('Selected IDs:', selectedIds);

        // Konfirmasi sebelum print
        Swal.fire({
            title: 'Konfirmasi Print Kartu',
            html: `Akan mencetak kartu untuk <strong>${selectedIds.length}</strong> anggota yang dipilih`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#dd6b55',
            confirmButtonText: 'Ya, Print Kartu',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Kirim data ke controller
                printKartuAnggota(selectedIds);
            }
        });

        return false;
    });

    $('#hapus_permanen').click(function() {
        var form = $('#form_items');
        var serialize_bulk = form.serialize();
        var url = "<?= base_url('anggota/hapus_permanen') ?>" + '?' + serialize_bulk;
        console.log(serialize_bulk);
        console.log(url);

        Swal.fire({
            title: 'Anda yakin?',
            html: "Semua anggota yang terpilih akan dihapus secara permanen",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#dd6b55',
            confirmButtonText: '<?= lang('App.btn.yes') ?>',
            cancelButtonText: '<?= lang('App.btn.no') ?>'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
        return false;
    });

    // Fungsi untuk mengirim data ke controller print kartu
    function printKartuAnggota(ids) {
        // Method 1: Kirim via POST dengan form hidden
        var form = $('<form></form>');
        form.attr('method', 'post');
        form.attr('action', '<?= base_url('anggota/multipleprint') ?>');
        form.attr('target', '_blank'); // Buka di tab baru

        // Tambahkan CSRF token jika ada
        <?php if (csrf_token()): ?>
            form.append('<input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" />');
        <?php endif; ?>

        // Tambahkan IDs sebagai array
        $.each(ids, function(index, value) {
            form.append('<input type="hidden" name="member_ids[]" value="' + value + '" />');
        });

        // Append form ke body dan submit
        $('body').append(form);
        form.submit();
        form.remove();
    }

    $("body").on("click", ".remove-data", function() {
        var url = $(this).attr('data-href');
        console.log(url);
        Swal.fire({
            title: '<?= lang('App.swal.are_you_sure') ?>',
            text: "<?= lang('App.swal.can_not_be_restored') ?>",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#dd6b55',
            confirmButtonText: '<?= lang('App.btn.yes') ?>',
            cancelButtonText: '<?= lang('App.btn.no') ?>'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
        return false;
    });

    // Peringatan (misalnya email gagal terkirim) tidak ditutup otomatis supaya sempat dibaca
    function show_result(res) {
        Swal.fire({
            title: res.warning ? 'Tersimpan, email belum terkirim' : 'Berhasil',
            html: res.message,
            icon: res.warning ? 'warning' : 'success',
            showConfirmButton: !!res.warning,
            confirmButtonText: 'Mengerti',
            timer: res.warning ? undefined : 5000,
        });
    }

    $(document).on('click', '.kirim-ulang-email', function() {
        var $button = $(this).prop('disabled', true);
        var label = $button.html();
        $button.text('Mengirim...');

        $.post($button.attr('data-href'))
            .done(function(res) {
                if (res.error) {
                    Swal.fire({ title: 'Email belum terkirim', text: res.message, icon: 'error', confirmButtonText: 'Mengerti' });
                    return;
                }
                t.ajax.reload(null, false);
                show_result(res);
            })
            .fail(function() {
                Swal.fire({ title: 'Email belum terkirim', text: 'Server tidak menjawab. Coba lagi.', icon: 'error', confirmButtonText: 'Mengerti' });
            })
            .always(function() {
                $button.prop('disabled', false).html(label);
            });
    });

    function ajax_post(url, data_post) {
        $.ajax({
                url: url,
                type: 'POST',
                data: data_post,
            })
            .done(function(res) {
                console.log(res)

                if (res.error == false) {
                    t.ajax.reload(null, false);
                    show_result(res);
                } else {
                    Swal.fire({
                        title: 'Gagal',
                        text: res.message,
                        icon: 'error',
                        showConfirmButton: false,
                        timer: 5000
                    }).then(() => {});
                }
            })
            .fail(function(res) {
                console.log(res);

                Swal.fire({
                    title: 'Oups',
                    text: 'Maaf, terjadi kesalahan. Coba beberapa saat lagi atau hubungi Admin',
                    icon: 'error',
                    showConfirmButton: false,
                    timer: 5000
                }).then(() => {});
            });
    }

    // Event handler untuk tombol Aktifkan Online
    $('#aktifkan_online').click(function() {
        var checkedBoxes = $('#tbl_data input[type="checkbox"]:checked').not('.check_data');
        
        console.log('Checked boxes found:', checkedBoxes.length);
        
        if (checkedBoxes.length === 0) {
            Swal.fire({
                title: 'Peringatan',
                text: 'Silakan pilih minimal satu anggota untuk diaktifkan secara online',
                icon: 'warning',
                showConfirmButton: true,
                confirmButtonText: 'OK'
            });
            return false;
        }
        
        // Collect all selected IDs
        var selectedIds = [];
        checkedBoxes.each(function() {
            var checkboxValue = $(this).val();
            if (checkboxValue && checkboxValue !== 'on') {
                selectedIds.push(checkboxValue);
            }
        });
        
        console.log('Selected IDs for online activation:', selectedIds);
        
        // Konfirmasi sebelum aktifkan
        Swal.fire({
            title: 'Konfirmasi Aktivasi Online',
            html: `Akan mengaktifkan akses online untuk <strong>${selectedIds.length}</strong> anggota yang dipilih.<br><br>
                   <small class="text-muted">Username akan sama dengan No. Anggota, dan password default sama dengan username.</small>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#dd6b55',
            confirmButtonText: 'Ya, Aktifkan',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Kirim data ke controller
                aktifkanAnggotaOnline(selectedIds);
            }
        });
        
        return false;
    });

    // Fungsi untuk mengirim data aktivasi online ke controller
    function aktifkanAnggotaOnline(ids) {
        // Tampilkan loading
        Swal.fire({
            title: 'Memproses...',
            text: 'Sedang mengaktifkan anggota secara online',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            willOpen: () => {
                Swal.showLoading();
            }
        });
        
        $.ajax({
            url: '<?= base_url('anggota/aktifkan_online') ?>',
            type: 'POST',
            data: {
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>',
                member_ids: ids
            },
            dataType: 'json'
        })
        .done(function(response) {
            console.log('Response:', response);
            
            if (response.error === false) {
                Swal.fire({
                    title: 'Berhasil!',
                    html: response.message,
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(() => {
                    // Reload datatable
                    t.ajax.reload(null, false);
                    // Uncheck all checkboxes
                    $('#tbl_data input:checkbox').prop('checked', false);
                });
            } else {
                Swal.fire({
                    title: 'Gagal',
                    html: response.message,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        })
        .fail(function(jqXHR, textStatus, errorThrown) {
            console.log('Error:', textStatus, errorThrown);
            console.log('Response:', jqXHR.responseText);
            
            Swal.fire({
                title: 'Oops!',
                text: 'Terjadi kesalahan saat memproses data. Silakan coba lagi.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        });
    }

    // Modal Upload Foto - Initialize on button click
    $(document).on('click', '.btn-upload-foto', function(e) {
        e.preventDefault();
        var id = $(this).attr('data-id');
        var field = $(this).attr('data-field');
        var title = $(this).attr('data-title');
        var format = $(this).attr('data-format');
        var formatTitle = $(this).attr('data-format-title');
        
        $('#upload_id').val(id);
        $('#upload_field').val(field);
        $('#upload_title').val(title);
        $('#upload_title_span').html(title);
        $('#upload_data_format_title').html(formatTitle || 'Format (JPG|PNG). Max 1 Files @ 2MB');
        
        // Initialize dropzone
        if (Dropzone.instances.length > 0) {
            Dropzone.instances.forEach(dz => dz.destroy());
        }
        setDropzone('file_pendukung', 'anggota', '.png,.jpg,.jpeg', 1, 10);
        
        $('#modal_upload').modal('show');
    });

    // Modal Camera - Initialize on button click
    $(document).on('click', '.btn-ambil-foto', function(e) {
        e.preventDefault();
        var id = $(this).attr('data-id');
        $('#capture_id').val(id);
        $('#modal_camera').modal('show');
    });

    // Handle modal upload hidden
    $(document).on('hidden.bs.modal','#modal_upload', function (e) {
        if (Dropzone.instances.length > 0) {
            Dropzone.instances.forEach(dz => dz.destroy());
        }
        $('#form_upload_message').html('');
    });

    // Handle modal camera shown
    $(document).on('shown.bs.modal','#modal_camera', function (e) {
        // Startup camera when modal is shown
        var width = 350;
        var height = 0;
        var streaming = false;
        
        var video = document.getElementById('video');
        var canvas = document.getElementById('canvas');
        var photo = document.getElementById('photo');
        var camera_image = document.getElementById('camera_image');
        var startbutton = document.getElementById('startbutton');

        function startup() {
            navigator.mediaDevices.getUserMedia({video: true, audio: false})
            .then(function(stream) {
                video.srcObject = stream;
                video.play();
            })
            .catch(function(err) {
                console.log("An error occurred: " + err);
                Swal.fire({
                    title: 'Error',
                    text: 'Tidak dapat mengakses kamera: ' + err,
                    icon: 'error'
                });
            });

            video.addEventListener('canplay', function(ev){
                if (!streaming) {
                    height = video.videoHeight / (video.videoWidth/width);
                
                    if (isNaN(height)) {
                        height = width / (4/3);
                    }
                
                    video.setAttribute('width', width);
                    video.setAttribute('height', height);
                    canvas.setAttribute('width', width);
                    canvas.setAttribute('height', height);
                    streaming = true;
                }
            }, false);

            startbutton.addEventListener('click', function(ev){
                takepicture();
                ev.preventDefault();
            }, false);
        }

        function takepicture() {
            var context = canvas.getContext('2d');
            if (width && height) {
                canvas.width = width;
                canvas.height = height;
                context.drawImage(video, 0, 0, width, height);
                
                var data = canvas.toDataURL('image/png');
                photo.setAttribute('src', data);
                camera_image.setAttribute('value', data);
            }
        }

        startup();
    });

    // Handle modal camera hidden
    $(document).on('hidden.bs.modal','#modal_camera', function (e) {
        var video = document.getElementById('video');
        if (video && video.srcObject) {
            video.srcObject.getTracks().forEach(track => track.stop());
        }
    });
</script>

<?= $this->endSection('script'); ?>