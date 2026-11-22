// Tombol Keranjang di OPAC untuk anggota yang login: kartu rak, modal preview, halaman detail, dan hitungan di navbar.
// Markup: opacTombolKeranjang() di app/Helpers/opac_helper.php. Endpoint: Anggota::ubahKeranjang (membalas JSON bila dipanggil lewat fetch).
(function () {
    'use strict';

    const cfg = window.OPAC_KERANJANG;
    const ids = new Set(cfg.ids);
    const status = document.getElementById('keranjangStatus');
    const modalEl = document.getElementById('opacBookMetaModal');

    function sinkron(tombol) {
        const aktif = ids.has(Number(tombol.dataset.keranjang));
        const label = aktif ? tombol.dataset.on : tombol.dataset.off;
        tombol.setAttribute('aria-pressed', aktif ? 'true' : 'false');
        const teks = tombol.querySelector('[data-teks]');
        if (teks) {
            teks.textContent = label;
        } else {
            const penuh = label + (tombol.dataset.judul ? ': ' + tombol.dataset.judul : '');
            tombol.setAttribute('aria-label', penuh);
            tombol.title = penuh;
        }
    }

    function sinkronSemua(id) {
        document.querySelectorAll('[data-keranjang]').forEach((tombol) => {
            if (id === undefined || Number(tombol.dataset.keranjang) === id) sinkron(tombol);
        });
        document.querySelectorAll('[data-keranjang-hitung]').forEach((el) => {
            el.textContent = ids.size;
            el.hidden = ids.size === 0;
        });
        const tautan = document.querySelector('.gn-keranjang');
        if (tautan) tautan.setAttribute('aria-label', 'Keranjang, ' + ids.size + ' buku');
    }

    document.addEventListener('click', async (e) => {
        const tombol = e.target.closest('[data-keranjang]');
        const id = tombol ? Number(tombol.dataset.keranjang) : 0;
        if (!id || tombol.getAttribute('aria-busy') === 'true') return;

        tombol.setAttribute('aria-busy', 'true');
        try {
            const body = new FormData();
            body.append('catalog_id', id);
            const res = await fetch(cfg.url, {
                method: 'POST',
                body,
                credentials: 'same-origin',
                headers: { 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json' },
            });
            if (res.status === 401) {
                location.href = cfg.masuk;
                return;
            }
            const data = await res.json();
            if (!res.ok) throw new Error(data.pesan);

            if (data.aktif) ids.add(id); else ids.delete(id);
            sinkronSemua(id);
            status.textContent = (tombol.dataset.judul ? tombol.dataset.judul + ': ' : '') + data.pesan;
        } catch (err) {
            status.textContent = 'Keranjang gagal diperbarui. Coba lagi.';
        } finally {
            tombol.removeAttribute('aria-busy');
        }
    });

    // Modal preview memakai satu tombol untuk semua buku: opac_modal.js mengisi id-nya lalu memberi tahu lewat event ini.
    if (modalEl) modalEl.addEventListener('opac:isi', () => sinkronSemua());
})();
