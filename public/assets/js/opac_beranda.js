// OPAC beranda: panel Filter dan Abjad, pilihan Judul/Pengarang di panel Abjad, dan carousel rak buku.
// Markup: app/Modules/Opac/Views/index.php dan app/Helpers/opac_beranda_helper.php.
(function () {
    'use strict';

    // ─── Panel Filter dan Abjad: satu terbuka pada satu waktu ───
    const tombolPanel = document.querySelectorAll('[data-panel-toggle]');
    const panelDari = (tombol) => document.getElementById(tombol.getAttribute('aria-controls'));

    function aturPanel(tombol, buka) {
        const panel = panelDari(tombol);
        if (!panel) return;
        panel.hidden = !buka;
        tombol.setAttribute('aria-expanded', buka ? 'true' : 'false');
    }

    tombolPanel.forEach((tombol) => {
        tombol.addEventListener('click', () => {
            const buka = tombol.getAttribute('aria-expanded') !== 'true';
            tombolPanel.forEach((lain) => aturPanel(lain, lain === tombol && buka));
        });
    });

    document.querySelectorAll('[data-panel-close]').forEach((x) => {
        x.addEventListener('click', () => {
            const tombol = document.querySelector('[aria-controls="' + x.getAttribute('data-panel-close') + '"]');
            if (!tombol) return;
            aturPanel(tombol, false);
            tombol.focus();
        });
    });

    document.addEventListener('keydown', (e) => {
        if (e.key !== 'Escape') return;
        const terbuka = Array.from(tombolPanel).find((t) => t.getAttribute('aria-expanded') === 'true');
        if (!terbuka) return;
        aturPanel(terbuka, false);
        terbuka.focus();
    });

    // ─── Panel Abjad: pilih huruf awal Judul atau Pengarang ───
    const modeAbjad = document.querySelectorAll('[data-abjad-mode]');
    modeAbjad.forEach((tombol) => {
        tombol.addEventListener('click', () => {
            const mode = tombol.getAttribute('data-abjad-mode');
            modeAbjad.forEach((x) => x.setAttribute('aria-pressed', x === tombol ? 'true' : 'false'));
            document.querySelectorAll('[data-abjad-grup]').forEach((g) => {
                g.hidden = g.getAttribute('data-abjad-grup') !== mode;
            });
        });
    });

    // ─── Carousel rak buku ───
    const kurangGerak = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    document.querySelectorAll('[data-carousel]').forEach((rak) => {
        const track = rak.querySelector('[data-carousel-track]');
        if (!track) return;
        const prev = rak.querySelector('[data-carousel-prev]');
        const next = rak.querySelector('[data-carousel-next]');
        const hitung = rak.querySelector('[data-carousel-count]');
        const kontrol = [prev, next, hitung].filter(Boolean);
        let menunggu = false;

        function perbarui() {
            menunggu = false;
            const maks = track.scrollWidth - track.clientWidth;
            // Semua kartu muat dalam satu layar: tombol geser tidak berguna.
            kontrol.forEach((el) => { el.hidden = maks <= 1; });
            if (maks <= 1) return;

            const halaman = Math.max(2, Math.ceil(track.scrollWidth / track.clientWidth - 0.05));
            const sekarang = Math.round((track.scrollLeft / maks) * (halaman - 1)) + 1;
            if (hitung) hitung.textContent = sekarang + ' / ' + halaman;
            if (prev) prev.disabled = track.scrollLeft <= 1;
            if (next) next.disabled = track.scrollLeft >= maks - 1;
        }

        // Geser sebanyak kartu yang muat di layar (minimal satu), kelipatan lebar kartu supaya pas dengan scroll-snap.
        function geser(arah) {
            const kartu = track.querySelector('.ob-card');
            const satu = kartu ? kartu.getBoundingClientRect().width + (parseFloat(getComputedStyle(track).columnGap) || 0) : track.clientWidth;
            const jarak = Math.max(1, Math.floor(track.clientWidth / satu)) * satu;
            track.scrollBy({ left: arah * jarak, behavior: kurangGerak ? 'auto' : 'smooth' });
        }

        if (prev) prev.addEventListener('click', () => geser(-1));
        if (next) next.addEventListener('click', () => geser(1));
        track.addEventListener('scroll', () => {
            if (!menunggu) {
                menunggu = true;
                requestAnimationFrame(perbarui);
            }
        }, { passive: true });
        window.addEventListener('resize', perbarui);
        perbarui();
    });
})();
