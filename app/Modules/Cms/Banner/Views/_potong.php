<?php
// Potong foto banner Beranda ke bingkai foto hero (610 x 540) sebelum diunggah.
// Bila skrip ini gagal dimuat, Banner::potongBingkaiHero() memotong di server dari tengah.
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css">
<style>
    .potong-kotak { width: 100%; max-width: 640px; height: 460px; background: #f1f3f6; }
    .potong-kotak img { display: block; max-width: 100%; }
    .potong-info { max-width: 640px; }
</style>
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js"></script>
<script>
    (function() {
        var RASIO = 610 / 540, LEBAR = 1220, TINGGI = 1080;
        var input = document.getElementById('file_cover');
        var kategori = document.getElementById('category');
        var box = document.getElementById('preview_cover_container');
        var form = input.form;
        var cropper = null, namaAsli = '', sudahDipotong = false;

        function bersih() {
            if (cropper) { cropper.destroy(); cropper = null; }
            box.innerHTML = '';
        }

        function info() {
            var d = cropper.getData(true), asli = cropper.getImageData();
            var teks = 'Foto asli ' + asli.naturalWidth + ' × ' + asli.naturalHeight + ' px. Bagian yang dipakai ' + d.width + ' × ' + d.height + ' px.';
            var kecil = d.width < LEBAR;
            document.getElementById('potong-info').innerHTML = teks + (kecil ? ' <b class="text-danger">Terlalu kecil, hasil bisa buram. Pakai foto lebar minimal ' + LEBAR + ' px.</b>' : ' <span class="text-success">Cukup tajam.</span>');
        }

        function tampil() {
            bersih();
            var file = input.files && input.files[0];
            if (!file) return;
            if (kategori.value !== 'Beranda' || !window.Cropper) { previewFile(input, 'preview_cover_container'); return; }
            namaAsli = file.name;
            box.innerHTML = '<div class="w-100"><div class="potong-kotak"><img id="potong-gambar" alt="Foto yang akan dipotong"></div>' +
                '<p class="potong-info form-text text-muted">Geser foto dan atur perbesarannya (roda mouse) sampai bagian yang diinginkan masuk bingkai. Bagian di luar bingkai dibuang.</p>' +
                '<p class="potong-info form-text" id="potong-info"></p></div>';
            var img = document.getElementById('potong-gambar');
            img.onload = function() {
                cropper = new Cropper(img, {
                    aspectRatio: RASIO, viewMode: 1, dragMode: 'move', autoCropArea: 1,
                    cropBoxMovable: false, cropBoxResizable: false, toggleDragModeOnDblclick: false,
                    ready: info, crop: info
                });
            };
            img.src = URL.createObjectURL(file);
        }

        input.addEventListener('change', function() { sudahDipotong = false; tampil(); });
        kategori.addEventListener('change', function() { sudahDipotong = false; tampil(); });

        form.addEventListener('submit', function(e) {
            if (!cropper || sudahDipotong) return;
            e.preventDefault();
            var pengirim = e.submitter;
            cropper.getCroppedCanvas({ width: LEBAR, height: TINGGI, imageSmoothingQuality: 'high' }).toBlob(function(blob) {
                var dt = new DataTransfer();
                dt.items.add(new File([blob], namaAsli.replace(/\.\w+$/, '') + '.jpg', { type: 'image/jpeg' }));
                input.files = dt.files;
                sudahDipotong = true;
                form.requestSubmit(pengirim);
            }, 'image/jpeg', 0.88);
        });
    })();
</script>
