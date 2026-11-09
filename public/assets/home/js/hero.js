// Hero: liquid glass di dua floating card, lengkung foto, dan kemunculan card setelah layar pemuatan hilang.
(function () {
    // Liquid glass untuk dua floating card di foto hero: tepi glass membiaskan background di belakangnya
    // seperti lensa pembesar. Tiap elemen punya filter SVG sendiri (peta pergeseran digambar sesuai ukurannya).
    // backdrop-filter dengan url() hanya jalan di Chromium; peramban lain tetap memakai kaca buram biasa (lihat CSS).
    var buatLensa = window.pkLensa; // dibuat navbar.js (hanya Chromium)
    if (buatLensa) {
        var lensa = [
            buatLensa('lens-kartu-atas', document.querySelector('.card-top'), null, 1.5, 26),
            buatLensa('lens-kartu-bawah', document.querySelector('.card-bottom'), null, 1.5, 26)
        ].filter(Boolean);
        var gambarLensa = function () { lensa.forEach(function (f) { f(); }); };
        gambarLensa();
        var lensTimer;
        window.addEventListener('resize', function () { clearTimeout(lensTimer); lensTimer = setTimeout(gambarLensa, 120); });
        window.addEventListener('load', gambarLensa);
    }

    // Lengkung kiri foto hero: sudut atasnya bundar (48% lebar dan tinggi), jadi di ketinggian kartu atas dan lingkaran hias
    // tepi foto menjorok ke dalam. Jorokannya dihitung supaya pusat kartu dan lingkaran tepat berada di garis tepi foto.
    var heroVisual = document.querySelector('.hero-visual');
    function lengkungTepi() {
        var foto = heroVisual && heroVisual.querySelector('.hero-image-wrap');
        if (!foto) return;
        var w = foto.offsetWidth, h = foto.offsetHeight, a = 0.48 * w, b = 0.48 * h;
        function jorok(el) {
            if (!el || !b) return 0;
            var y = el.offsetTop + el.offsetHeight / 2 - foto.offsetTop;
            if (y >= b) return 0;
            var t = (b - y) / b;
            return a * (1 - Math.sqrt(Math.max(0, 1 - t * t)));
        }
        heroVisual.style.setProperty('--lengkung-kartu', jorok(heroVisual.querySelector('.card-top')).toFixed(1) + 'px');
        heroVisual.style.setProperty('--lengkung-orbit', jorok(heroVisual.querySelector('.orbit-one')).toFixed(1) + 'px');
    }
    lengkungTepi();
    window.addEventListener('resize', lengkungTepi);
    window.addEventListener('load', lengkungTepi);

    var KARTU_TAMPIL_MS = 8000; // lama kartu pembaruan terlihat sebelum pamit
    // Kartu melayang di hero masuk setelah layar pemuatan hilang. Kelas .js-kartu menyembunyikan kartu sampai saat itu;
    // tanpa JavaScript kelas itu tidak ada dan kartu langsung terlihat.
    if (heroVisual && heroVisual.querySelector('.floating-card')) {
        document.documentElement.classList.add('js-kartu');
        var munculkanKartu = function () {
            heroVisual.classList.add('kartu-masuk');
            lengkungTepi();
            // Kartu pembaruan tidak menetap: tampil sebentar, lalu pamit satu per satu.
            heroVisual.querySelectorAll('.floating-card').forEach(function (kartu, i) {
                setTimeout(function () { kartu.classList.add('is-pergi'); }, KARTU_TAMPIL_MS + i * 600);
            });
        };
        var pemuat = document.getElementById('pk-loader');
        if (!pemuat || pemuat.classList.contains('pk-selesai')) {
            munculkanKartu();
        } else {
            new MutationObserver(function (_, pengamat) {
                if (pemuat.classList.contains('pk-selesai') || !document.body.contains(pemuat)) { pengamat.disconnect(); setTimeout(munculkanKartu, 250); }
            }).observe(pemuat, { attributes: true, attributeFilter: ['class'] });
            setTimeout(munculkanKartu, 9000);
        }
    }
})();
