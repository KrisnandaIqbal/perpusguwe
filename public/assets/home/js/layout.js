// Skala halaman: memperbesar seluruh halaman di layar lebar dan menghitung tinggi minimum hero.
(function () {
    // Skala tampilan: desain dibuat untuk lebar sekitar 1420 px. Di layar yang lebih lebar, seluruh halaman
    // diperbesar sebanding supaya proporsinya sama dengan desain. SKALA mengalikan semuanya (1.1 = 10% lebih besar).
    // 0.75: layar sampai sekitar 1890 px tampil seperti desain (tanpa pembesaran); baru di atas itu halaman diperbesar.
    var SKALA = 0.75;
    var DESAIN = 1420;
    var frame = document.querySelector('.site-frame');
    function applyZoom() {
        var width = window.innerWidth;
        var zoom = width >= 1024 ? Math.max(1, Math.min(2, width / DESAIN * SKALA)) : 1;
        frame.style.zoom = zoom === 1 ? '' : zoom.toFixed(3);
        // Tinggi minimum hero: header + hero mengisi tinggi layar, jadi bagian biru di bawahnya tidak mengintip.
        // Satuan vh tidak ikut diperkecil oleh zoom, maka dihitung di sini dalam piksel CSS milik .site-frame.
        var siteHeader = document.querySelector('.gn-bar');
        var heroMin = siteHeader ? Math.round(window.innerHeight / zoom - siteHeader.getBoundingClientRect().height / zoom) : 0;
        frame.style.setProperty('--hero-min', Math.max(0, heroMin) + 'px');
    }
    applyZoom();
    window.addEventListener('resize', applyZoom);
})();
