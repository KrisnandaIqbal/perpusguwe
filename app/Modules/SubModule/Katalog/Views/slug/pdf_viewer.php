<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'Baca Digital - Flipbook') ?></title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?= base_url('assets/vendors/fontawesome-pro-5/css/all.min.css'); ?>">
    <!-- PDF Flipbook CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/vendors/pflip/css/pdfflip.css'); ?>">

    <style>
        * {
            box-sizing: border-box;
        }
        html, body {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            background-color: #1a1e24;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            color: #f8f9fa;
        }

        /* Proteksi anti-select dan anti-drag */
        body, #reader-container, #PDFF {
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            -webkit-touch-callout: none;
        }

        /* Larang mencetak / print to PDF */
        @media print {
            body {
                display: none !important;
            }
        }

        /* Top Bar Header */
        #reader-header {
            height: 54px;
            background: linear-gradient(180deg, #212529 0%, #1a1d20 100%);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 18px;
            z-index: 100;
            position: relative;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
        }

        .header-title-wrap {
            display: flex;
            align-items: center;
            gap: 12px;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
            max-width: 70%;
        }

        .header-title-wrap i {
            color: #0d6efd;
            font-size: 1.25rem;
            flex-shrink: 0;
        }

        .header-title {
            font-size: 0.95rem;
            font-weight: 600;
            color: #ffffff;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .header-badge {
            font-size: 0.72rem;
            font-weight: 500;
            background: rgba(13, 110, 253, 0.2);
            color: #6ea8fe;
            border: 1px solid rgba(13, 110, 253, 0.4);
            padding: 2px 8px;
            border-radius: 12px;
            flex-shrink: 0;
            letter-spacing: 0.3px;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-header {
            background: rgba(255, 255, 255, 0.08);
            color: #e9ecef;
            border: 1px solid rgba(255, 255, 255, 0.15);
            padding: 6px 14px;
            font-size: 0.85rem;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s ease;
        }

        .btn-header:hover {
            background: rgba(255, 255, 255, 0.18);
            color: #ffffff;
            border-color: rgba(255, 255, 255, 0.3);
        }

        .btn-header-danger {
            background: rgba(220, 53, 69, 0.15);
            color: #f8d7da;
            border-color: rgba(220, 53, 69, 0.3);
        }

        .btn-header-danger:hover {
            background: rgba(220, 53, 69, 0.35);
            color: #ffffff;
            border-color: rgba(220, 53, 69, 0.5);
        }

        /* Flipbook Container */
        #reader-container {
            width: 100%;
            height: calc(100% - 54px);
            position: relative;
            background: radial-gradient(circle at center, #2b313a 0%, #16191d 100%);
        }

        #PDFF {
            width: 100%;
            height: 100%;
        }

        /* Loading indicator styling */
        #reader-loading {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 10;
            color: #ffffff;
            text-align: center;
        }

        .loading-spinner {
            width: 48px;
            height: 48px;
            border: 4px solid rgba(255, 255, 255, 0.15);
            border-top-color: #0d6efd;
            border-radius: 50%;
            animation: pdff-spin 0.8s linear infinite;
            margin-bottom: 16px;
        }

        @keyframes pdff-spin {
            to { transform: rotate(360deg); }
        }

        .loading-text {
            font-size: 1rem;
            font-weight: 500;
            letter-spacing: 0.3px;
            color: #e9ecef;
        }

        .loading-subtext {
            font-size: 0.8rem;
            color: #adb5bd;
            margin-top: 4px;
        }

        /* Penyesuaian toolbar pdfflip */
        .pdff-container {
            font-family: inherit !important;
        }
    </style>
</head>
<body oncontextmenu="return false;">

    <!-- Header Navigation -->
    <header id="reader-header">
        <div class="header-title-wrap">
            <i class="fas fa-book-open"></i>
            <span class="header-title" title="<?= esc($title ?? 'Buku Digital') ?>"><?= esc($title ?? 'Buku Digital') ?></span>
            <span class="header-badge"><i class="fas fa-cube me-1"></i> Flipbook 3D</span>
        </div>
        <div class="header-actions">
            <button type="button" class="btn-header" onclick="toggleReaderFullscreen()" title="Layar Penuh">
                <i class="fas fa-expand"></i> <span class="d-none d-sm-inline">Layar Penuh</span>
            </button>
            <a href="javascript:void(0)" onclick="closeReader()" class="btn-header btn-header-danger" title="Tutup Pembaca">
                <i class="fas fa-times"></i> <span class="d-none d-sm-inline">Tutup</span>
            </a>
        </div>
    </header>

    <!-- Flipbook Workspace -->
    <main id="reader-container">
        <div id="reader-loading">
            <div class="loading-spinner"></div>
            <div class="loading-text">Menyiapkan Buku Digital...</div>
            <div class="loading-subtext">Memuat ke Flipbook 3D interaktif</div>
        </div>
        <div id="PDFF" style="display: none;"></div>
    </main>

    <!-- Dependencies -->
    <script src="<?= base_url('assets/vendors/pflip/js/libs/jquery.min.js'); ?>" type="text/javascript"></script>
    <!-- Main PDF Flip Plugin -->
    <script src="<?= base_url('assets/vendors/pflip/js/pdfflip.js'); ?>" type="text/javascript"></script>

    <script type="text/javascript">
        window.pdfflipLocation = '<?= base_url('assets/vendors/pflip'); ?>/';

        var option_PDFF = {
            openPage: 1,
            height: '100%',
            enableSound: true,
            downloadEnable: false,
            direction: pdfflip.DIRECTION.LTR,
            autoPlay: false,
            autoPlayStart: false,
            autoPlayDuration: 3000,
            autoEnableOutline: false,
            autoEnableThumbnail: false,
            /* Terjemahan Bahasa Indonesia */
            text: {
                toggleSound: "Suara Balik Lembar",
                toggleThumbnails: "Pratinjau Halaman",
                toggleOutline: "Daftar Isi",
                previousPage: "Halaman Sebelumnya",
                nextPage: "Halaman Selanjutnya",
                toggleFullscreen: "Layar Penuh",
                zoomIn: "Perbesar",
                zoomOut: "Perkecil",
                downloadPDFFile: "Unduh PDF",
                gotoFirstPage: "Halaman Pertama",
                gotoLastPage: "Halaman Terakhir",
                play: "Putar Otomatis",
                pause: "Jeda Otomatis",
                share: "Bagikan"
            },
            /* Pengaturan Animasi & Efek Flip */
            hard: "none",
            overwritePDFOutline: true,
            duration: 800,
            pageMode: pdfflip.PAGE_MODE.AUTO,
            singlePageMode: pdfflip.SINGLE_PAGE_MODE.BOOKLET,
            transparent: false,
            scrollWheel: true,
            zoomRatio: 1.5,
            maxTextureSize: 1600,
            backgroundColor: "#20252b",
            controlsPosition: pdfflip.CONTROLSPOSITION.BOTTOM,
            allControls: "thumbnail,altPrev,pageNumber,altNext,zoomIn,zoomOut,sound,fullScreen",
            hideControls: "download,share,play,more"
        };

        var currentBlobUrl = null;

        // Muat file sebagai binary stream in-memory via fetch
        // Pendekatan ini mencegah IDM / browser download manager mencegat unduhan
        $(document).ready(function() {
            var endpoint = '<?= base_url('katalog/get_decrypted_content/' . encData((string) $fileId)); ?>';

            fetch(endpoint, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(function(res) {
                if (!res.ok) {
                    throw new Error('Gagal memuat dokumen (Status: ' + res.status + ')');
                }
                return res.arrayBuffer();
            })
            .then(function(buffer) {
                var blob = new Blob([buffer], { type: 'application/pdf' });
                currentBlobUrl = URL.createObjectURL(blob);

                $('#reader-loading').fadeOut(200, function() {
                    $('#PDFF').show();
                    $('#PDFF').flipBook(currentBlobUrl, option_PDFF);
                });
            })
            .catch(function(err) {
                console.error('Flipbook load error:', err);
                $('#reader-loading').html(
                    '<div style="text-align: center; color: #ff6b6b; padding: 20px;">' +
                        '<i class="fas fa-exclamation-triangle fa-3x mb-3"></i>' +
                        '<div style="font-size: 1.1rem; font-weight: 600; margin-bottom: 6px;">Gagal Menampilkan Flipbook</div>' +
                        '<div style="font-size: 0.85rem; color: #adb5bd;">' + (err.message || 'Terjadi gangguan saat memuat file.') + '</div>' +
                    '</div>'
                );
            });
        });

        // Bersihkan memori Blob URL saat jendela ditutup
        window.addEventListener('beforeunload', function() {
            if (currentBlobUrl) {
                URL.revokeObjectURL(currentBlobUrl);
            }
        });

        function toggleReaderFullscreen() {
            var btn = $('.pdff-ui-fullscreen');
            if (btn.length > 0) {
                btn.trigger('click');
            } else {
                if (!document.fullscreenElement) {
                    document.documentElement.requestFullscreen().catch(function() {});
                } else if (document.exitFullscreen) {
                    document.exitFullscreen();
                }
            }
        }

        function closeReader() {
            if (window.opener && !window.opener.closed) {
                window.close();
            } else if (window.history.length > 1) {
                window.history.back();
            } else {
                window.location.href = '<?= base_url('opac') ?>';
            }
        }

        // Nonaktifkan klik kanan untuk mencegah unduh / save image / inspeksi
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
            return false;
        });

        // Nonaktifkan tombol pintas Ctrl+S, Ctrl+P, Ctrl+U, F12
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && (e.key === 's' || e.key === 'p' || e.key === 'u' || e.key === 'S' || e.key === 'P' || e.key === 'U')) {
                e.preventDefault();
                return false;
            }
            if (e.key === 'F12' || ((e.ctrlKey || e.metaKey) && e.shiftKey && (e.key === 'I' || e.key === 'i' || e.key === 'J' || e.key === 'j' || e.key === 'C' || e.key === 'c'))) {
                e.preventDefault();
                return false;
            }
        });

        // Nonaktifkan drag & drop
        document.addEventListener('dragstart', function(e) {
            e.preventDefault();
            return false;
        });
    </script>

</body>
</html>
