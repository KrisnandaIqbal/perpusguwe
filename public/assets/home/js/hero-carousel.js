// Carousel foto hero (vertikal): titik pilihan, geser, tombol panah, dan geser otomatis.
(function () {
    // Carousel foto hero (vertikal): foto bergeser ke atas atau bawah. Bisa dipilih lewat titik, digeser (mouse atau jari),
    // atau memakai tombol panah atas/bawah; otomatis tiap JEDA_FOTO_MS dan berhenti saat kursor atau fokus ada di foto dan
    // saat tab tidak terlihat. Pengunjung yang memilih "kurangi gerakan" tidak mendapat geseran otomatis.
    // Tiap foto punya posisi bilangan bulat --o (0 = tampil, 1 = di bawah, -1 = di atas). Foto yang melompat dari satu ujung
    // ke ujung lain berpindah tanpa animasi, saat sedang di luar bingkai.
    var JEDA_FOTO_MS = 6000;
    var slideFoto = [].slice.call(document.querySelectorAll('.hero-slide'));
    if (slideFoto.length > 1) {
        var titikFoto = [].slice.call(document.querySelectorAll('.hero-dot'));
        var tetes = document.querySelector('.hero-tetes');
        var labelFoto = document.querySelector('[data-hero-label]');
        var bingkaiFoto = document.querySelector('.hero-image-wrap');
        var lajurFoto = document.querySelector('.hero-slides');
        var fotoKe = 0, fotoDitahan = false, fotoDiseret = false, tetesTimer;
        var posisiFoto = slideFoto.map(function (_, i) { return i === 0 ? 0 : (i === slideFoto.length - 1 && slideFoto.length > 2 ? -1 : 1); });
        var JARAK_TITIK = 30, UKURAN_TITIK = 10, TEPI_TITIK = 10;
        var aturPosisi = function (arah) {
            var n = slideFoto.length;
            slideFoto.forEach(function (el, i) {
                var d = (i - fotoKe + n) % n, o = d > n / 2 ? d - n : d;
                if (n % 2 === 0 && d === n / 2) o = arah > 0 ? -d : d;
                el.classList.toggle('tanpa-gerak', Math.abs(o - posisiFoto[i]) > 1);
                el.style.setProperty('--o', o);
                el.setAttribute('aria-hidden', o === 0 ? 'false' : 'true');
                posisiFoto[i] = o;
            });
        };
        // Lingkaran putih menetes: memanjang menutup titik asal sampai titik tujuan, lalu menyusut ke titik tujuan.
        var pindahkanTetes = function (dari, ke) {
            if (!tetes) return;
            clearTimeout(tetesTimer);
            if (dari === ke) { tetes.style.top = (TEPI_TITIK + ke * JARAK_TITIK) + 'px'; tetes.style.height = UKURAN_TITIK + 'px'; return; }
            tetes.style.top = (TEPI_TITIK + Math.min(dari, ke) * JARAK_TITIK) + 'px';
            tetes.style.height = (Math.abs(ke - dari) * JARAK_TITIK + UKURAN_TITIK) + 'px';
            tetesTimer = setTimeout(function () {
                tetes.style.top = (TEPI_TITIK + ke * JARAK_TITIK) + 'px';
                tetes.style.height = UKURAN_TITIK + 'px';
            }, 230);
        };
        var tampilkanFoto = function (n, arah) {
            var dari = fotoKe;
            fotoKe = (n + slideFoto.length) % slideFoto.length;
            aturPosisi(arah || 1);
            pindahkanTetes(dari, fotoKe);
            titikFoto.forEach(function (el, i) { el.classList.toggle('is-active', i === fotoKe); el.setAttribute('aria-pressed', i === fotoKe ? 'true' : 'false'); });
            if (labelFoto) labelFoto.textContent = String(fotoKe + 1).padStart(2, '0') + ' / ' + slideFoto[fotoKe].getAttribute('data-label');
        };
        aturPosisi(1);
        pindahkanTetes(0, 0);
        titikFoto.forEach(function (el, i) { el.addEventListener('click', function () { tampilkanFoto(i, i > fotoKe ? 1 : -1); }); });

        // Keyboard: panah atas/bawah saat fokus ada di paginasi.
        document.querySelector('.hero-nav').addEventListener('keydown', function (e) {
            if (e.key !== 'ArrowUp' && e.key !== 'ArrowDown') return;
            e.preventDefault();
            var arah = e.key === 'ArrowDown' ? 1 : -1;
            tampilkanFoto(fotoKe + arah, arah);
            titikFoto[fotoKe].focus();
        });

        // Geser dengan mouse atau jari: foto mengikuti gerakan; dilepas melewati 18% tinggi bingkai (atau cukup cepat) berpindah,
        // kalau tidak foto kembali. Sentuhan hanya menangkap geseran dari area foto (touch-action: pan-x di CSS).
        var awalY = 0, tarikY = 0, awalWaktu = 0, sedangTarik = false;
        lajurFoto.addEventListener('pointerdown', function (e) {
            if (e.button !== undefined && e.button !== 0) return;
            sedangTarik = true; fotoDiseret = false; awalY = e.clientY; tarikY = 0; awalWaktu = Date.now();
            try { lajurFoto.setPointerCapture(e.pointerId); } catch (err) { /* diabaikan */ }
        });
        lajurFoto.addEventListener('pointermove', function (e) {
            if (!sedangTarik) return;
            tarikY = e.clientY - awalY;
            if (!fotoDiseret && Math.abs(tarikY) < 6) return;
            fotoDiseret = true;
            lajurFoto.classList.add('menyeret');
            lajurFoto.style.setProperty('--tarik', tarikY + 'px');
        });
        var lepas = function () {
            if (!sedangTarik) return;
            sedangTarik = false;
            lajurFoto.classList.remove('menyeret');
            lajurFoto.style.removeProperty('--tarik');
            if (!fotoDiseret) return;
            var tinggi = lajurFoto.offsetHeight || 1, cepat = Math.abs(tarikY) / Math.max(1, Date.now() - awalWaktu) > 0.5;
            if (Math.abs(tarikY) > tinggi * 0.18 || (cepat && Math.abs(tarikY) > 24)) {
                var arah = tarikY < 0 ? 1 : -1;  // ditarik ke atas = foto berikutnya (datang dari bawah)
                tampilkanFoto(fotoKe + arah, arah);
            }
            setTimeout(function () { fotoDiseret = false; }, 0);
        };
        lajurFoto.addEventListener('pointerup', lepas);
        lajurFoto.addEventListener('pointercancel', lepas);
        lajurFoto.addEventListener('dragstart', function (e) { e.preventDefault(); });

        bingkaiFoto.addEventListener('mouseenter', function () { fotoDitahan = true; });
        bingkaiFoto.addEventListener('mouseleave', function () { fotoDitahan = false; });
        bingkaiFoto.addEventListener('focusin', function () { fotoDitahan = true; });
        bingkaiFoto.addEventListener('focusout', function () { fotoDitahan = false; });
        if (!(window.matchMedia && matchMedia('(prefers-reduced-motion: reduce)').matches)) {
            setInterval(function () { if (!fotoDitahan && !sedangTarik && !document.hidden) tampilkanFoto(fotoKe + 1, 1); }, JEDA_FOTO_MS);
        }
    }
})();
