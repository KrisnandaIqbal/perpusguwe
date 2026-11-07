// Fungsi kecil yang dipakai berkas JS beranda lain. Harus dimuat paling awal.
window.Landing = {
    // Ubah teks jadi HTML aman (mencegah teks dari server tampil sebagai tag).
    esc: function (value) {
        var div = document.createElement('div');
        div.textContent = value == null ? '' : String(value);
        return div.innerHTML;
    },
    // Huruf kecil tanpa tanda diakritik, untuk mencocokkan kata kunci pencarian.
    normalize: function (value) {
        return String(value).toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
    }
};
