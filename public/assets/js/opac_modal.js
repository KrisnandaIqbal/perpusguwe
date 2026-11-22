// Modal preview buku OPAC. Markup: #opacBookMetaModal di app/Views/layout/opac/layout.php, CSS: assets/css/opac_modal.css.
// Data buku dibaca dari atribut data-book-meta pada kartu (dibuat opacBookMetaAttr() di app/Helpers/opac_helper.php).
document.addEventListener('DOMContentLoaded', function () {
    const modalEl = document.getElementById('opacBookMetaModal');
    if (!modalEl) return;

    const $ = (id) => document.getElementById(id);
    let pemicu = null;

    document.addEventListener('click', function (e) {
        const link = e.target.closest('.js-open-book-modal');
        // Klik dengan Ctrl, Cmd, Shift, atau tombol tengah tetap membuka halaman detail di tab baru.
        if (!link || e.button !== 0 || e.ctrlKey || e.metaKey || e.shiftKey || e.altKey) return;

        const kartu = link.closest('[data-book-meta]');
        if (!kartu) return;

        try {
            isi(JSON.parse(kartu.dataset.bookMeta));
        } catch (err) {
            return; // tanpa e.preventDefault(): tautan membuka halaman detail
        }
        e.preventDefault();
        pemicu = link;
        bootstrap.Modal.getOrCreateInstance(modalEl).show();
    });

    // Bootstrap 5.0 tidak mengembalikan fokus untuk modal yang dibuka lewat skrip.
    modalEl.addEventListener('hidden.bs.modal', function () {
        if (pemicu) pemicu.focus();
    });

    function isi(d) {
        $('omJudul').textContent = d.title || 'Tanpa judul';
        $('omPenulis').textContent = d.author || '';
        $('omPanggil').textContent = d.callNumber || '-';
        $('omRak').textContent = d.shelfLoc || 'Rak Koleksi Umum';
        $('omPenerbit').textContent = d.pubAndYear || '-';
        $('omIsbn').textContent = d.isbnAndEdition || '-';
        $('omDetail').href = d.detailUrl || '#';

        const keranjang = modalEl.querySelector('[data-keranjang]');
        if (keranjang) {
            keranjang.dataset.keranjang = d.id;
            keranjang.dataset.judul = d.title || '';
        }

        const stok = $('omStok');
        stok.className = 'om-stok om-stok-' + (d.stockState || 'kosong');
        stok.textContent = d.statusModalText || '';

        $('omRingkas').hidden = !d.abstract;
        $('omRingkasan').textContent = d.abstract || '';

        const subjek = Array.isArray(d.subjects) ? d.subjects.slice(0, 4) : [];
        $('omSubjekWrap').hidden = subjek.length === 0;
        $('omSubjek').replaceChildren(...subjek.map(function (s) {
            const li = document.createElement('li');
            const a = document.createElement('a');
            a.href = window.OPAC_URL + '?search=' + encodeURIComponent(s) + '&search_by=Subject';
            a.textContent = s;
            li.appendChild(a);
            return li;
        }));

        const foto = $('omSampulFoto');
        const buatan = $('omSampulBuatan');
        const tampilkanBuatan = function () {
            foto.hidden = true;
            buatan.hidden = false;
        };

        const gaya = d.coverStyle || { warna: ['#101521', '#f2b544'], terang: false };
        buatan.className = 'ob-cover' + (gaya.terang ? ' ob-cover-terang' : '');
        buatan.style.background = gaya.warna[0];
        buatan.style.color = gaya.warna[1];
        $('omSampulBulat').style.background = gaya.warna[1];
        $('omSampulKategori').textContent = d.topLabel || 'KOLEKSI';
        $('omSampulJudul').textContent = d.title || '';
        $('omSampulPenulis').textContent = d.author || '';

        if (d.hasCover && d.coverImgSrc) {
            foto.onerror = tampilkanBuatan;
            foto.src = d.coverImgSrc;
            foto.alt = 'Sampul ' + (d.title || 'buku');
            foto.hidden = false;
            buatan.hidden = true;
        } else {
            foto.removeAttribute('src');
            tampilkanBuatan();
        }

        modalEl.dispatchEvent(new Event('opac:isi'));
    }
});
