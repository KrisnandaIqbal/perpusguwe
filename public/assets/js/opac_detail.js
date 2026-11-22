// Halaman detail buku OPAC: tab ketersediaan, saring eksemplar, lompat ke ketersediaan, dan salin tautan.
// Markup: app/Modules/Opac/Views/detail.php. CSS: assets/css/opac_detail.css.
(function () {
    'use strict';

    const kurangiGerak = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    // ─── Tab: Buku fisik, Koleksi digital, MARC21 ───
    const tabs = Array.from(document.querySelectorAll('.od-tab [role="tab"]'));

    function bukaTab(nama, fokus) {
        tabs.forEach((tab) => {
            const aktif = tab.dataset.tab === nama;
            tab.setAttribute('aria-selected', aktif ? 'true' : 'false');
            tab.tabIndex = aktif ? 0 : -1;
            document.getElementById(tab.getAttribute('aria-controls')).hidden = !aktif;
            if (aktif && fokus) tab.focus();
        });
    }

    document.querySelectorAll('[data-tab]').forEach((el) => {
        el.addEventListener('click', () => bukaTab(el.dataset.tab, false));
    });

    tabs.forEach((tab, i) => {
        tab.addEventListener('keydown', (e) => {
            const geser = { ArrowRight: 1, ArrowLeft: -1 }[e.key];
            if (e.key === 'Home' || e.key === 'End') {
                e.preventDefault();
                bukaTab(tabs[e.key === 'Home' ? 0 : tabs.length - 1].dataset.tab, true);
            } else if (geser) {
                e.preventDefault();
                bukaTab(tabs[(i + geser + tabs.length) % tabs.length].dataset.tab, true);
            }
        });
    });

    // ─── Lompat ke bagian ketersediaan ───
    document.querySelectorAll('[data-ke]').forEach((tombol) => {
        tombol.addEventListener('click', () => {
            bukaTab('fisik', false);
            document.getElementById(tombol.dataset.ke).scrollIntoView({ behavior: kurangiGerak ? 'auto' : 'smooth', block: 'start' });
        });
    });

    // ─── Saring eksemplar: status dan kata kunci ───
    const kotakCari = document.getElementById('inventorySearchInput');
    const tombolSaring = Array.from(document.querySelectorAll('.od-saring button'));
    const barisKosong = document.getElementById('inventoryEmptyRow');
    const barisEksemplar = Array.from(document.querySelectorAll('#inventoryTableBody tr.inventory-data-row'));
    let status = 'all';

    function saring() {
        const kata = kotakCari.value.trim().toLowerCase();
        let tampak = 0;
        barisEksemplar.forEach((baris) => {
            const cocok = (status === 'all' || baris.dataset.status === status) && baris.dataset.search.includes(kata);
            baris.hidden = !cocok;
            if (cocok) tampak++;
        });
        barisKosong.hidden = barisEksemplar.length === 0 ? false : tampak > 0;
    }

    tombolSaring.forEach((tombol) => {
        tombol.addEventListener('click', () => {
            status = tombol.dataset.status;
            tombolSaring.forEach((t) => t.setAttribute('aria-pressed', t === tombol ? 'true' : 'false'));
            saring();
        });
    });
    kotakCari.addEventListener('input', saring);

    // ─── Salin tautan ───
    const tombolSalin = document.getElementById('odSalin');
    const teksAwal = tombolSalin.textContent;

    function salin(teks) {
        if (navigator.clipboard && window.isSecureContext) return navigator.clipboard.writeText(teks);
        // Halaman tanpa HTTPS (mis. server lokal) tidak boleh memakai Clipboard API.
        return new Promise((ok, gagal) => {
            const area = document.createElement('textarea');
            area.value = teks;
            area.setAttribute('readonly', '');
            area.style.position = 'fixed';
            area.style.opacity = '0';
            document.body.appendChild(area);
            area.select();
            const berhasil = document.execCommand('copy');
            area.remove();
            berhasil ? ok() : gagal();
        });
    }

    tombolSalin.addEventListener('click', () => {
        salin(window.OPAC_DETAIL.url).then(
            () => { tombolSalin.textContent = 'Tautan disalin'; },
            () => { tombolSalin.textContent = 'Gagal menyalin'; }
        ).finally(() => setTimeout(() => { tombolSalin.textContent = teksAwal; }, 2000));
    });
})();
