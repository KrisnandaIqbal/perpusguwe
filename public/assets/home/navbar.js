// Navbar publik (partial layout/partial/navbar_publik.php): menu ponsel, menu turun, kaca saat digulir, dan kaca cair (Liquid Glass).
(function () {
    var bar = document.querySelector('.gn-bar');
    if (!bar) return;

    // Tinggi bar dipakai halaman untuk memberi ruang di bawah bar yang fixed.
    function ukurBar() { document.documentElement.style.setProperty('--nav-h', bar.offsetHeight + 'px'); }
    ukurBar();
    window.addEventListener('resize', ukurBar);

    // Kaca muncul saat halaman digulir; di halaman dalam (data-glass="always") selalu ada karena latarnya tidak seragam.
    function markStuck() { bar.classList.toggle('is-stuck', bar.dataset.glass === 'always' || window.scrollY > 24); }
    markStuck();
    window.addEventListener('scroll', markStuck, { passive: true });

    var nav = document.getElementById('gn-nav');
    var burger = document.getElementById('gn-burger');
    var drops = bar.querySelectorAll('.gn-drop');

    function setDrop(drop, open) {
        drop.classList.toggle('is-open', open);
        drop.querySelector('.gn-drop-btn').setAttribute('aria-expanded', open ? 'true' : 'false');
    }
    function closeDrops(except) { drops.forEach(function (d) { if (d !== except) setDrop(d, false); }); }
    function setMenu(open) {
        nav.classList.toggle('is-open', open);
        burger.setAttribute('aria-expanded', open ? 'true' : 'false');
        burger.setAttribute('aria-label', open ? 'Tutup menu' : 'Buka menu');
        burger.querySelector('[data-open]').hidden = open;
        burger.querySelector('[data-close]').hidden = !open;
    }

    burger.addEventListener('click', function () { setMenu(!nav.classList.contains('is-open')); });
    drops.forEach(function (drop) {
        drop.querySelector('.gn-drop-btn').addEventListener('click', function () {
            var open = !drop.classList.contains('is-open');
            closeDrops(drop);
            setDrop(drop, open);
        });
    });
    nav.querySelectorAll('a').forEach(function (link) {
        link.addEventListener('click', function () { closeDrops(); setMenu(false); });
    });
    document.addEventListener('pointerdown', function (event) {
        drops.forEach(function (d) { if (!d.contains(event.target)) setDrop(d, false); });
    });
    window.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') closeDrops();
    });

    // Kaca cair: tepi kaca membiaskan latar di belakangnya seperti lensa pembesar. backdrop-filter dengan url() hanya jalan
    // di Chromium; peramban lain memakai kaca buram biasa (lihat navbar.css). window.pkLensa dipakai juga oleh kartu hero di js/hero.js.
    var brands = (navigator.userAgentData && navigator.userAgentData.brands) || [];
    var chromium = brands.some(function (b) { return /Chromium/.test(b.brand); }) || (!navigator.userAgentData && /Chrome\//.test(navigator.userAgent));
    if (!chromium) return;
    var NS = 'http://www.w3.org/2000/svg';
    var lensSvg = document.createElementNS(NS, 'svg');
    lensSvg.setAttribute('width', '0'); lensSvg.setAttribute('height', '0'); lensSvg.setAttribute('aria-hidden', 'true');
    lensSvg.style.cssText = 'position:absolute;pointer-events:none';
    document.body.appendChild(lensSvg);

    var buatLensa = function (id, el, pseudo, blur, skala) {
        if (!el) return;
        lensSvg.insertAdjacentHTML('beforeend', '<filter id="' + id + '" x="0" y="0" width="100%" height="100%" color-interpolation-filters="sRGB">'
            + '<feGaussianBlur in="SourceGraphic" stdDeviation="' + blur + '" result="lembut"/>'
            + '<feImage x="0" y="0" width="10" height="10" preserveAspectRatio="none" result="peta"/>'
            + '<feDisplacementMap in="lembut" in2="peta" scale="' + skala + '" xChannelSelector="R" yChannelSelector="G"/>'
            + '</filter>');
        var peta = lensSvg.lastChild.querySelector('feImage'), kunci = '';
        return function () {
            var cs = getComputedStyle(el, pseudo || null);
            var w = Math.round(parseFloat(cs.width)), h = Math.round(parseFloat(cs.height));
            var r = Math.min(parseFloat(cs.borderTopLeftRadius) || 20, h / 2);
            if (!w || !h || w > 4000) return;
            var k = w + 'x' + h + 'r' + r;
            if (k === kunci) return;
            kunci = k;
            var c = document.createElement('canvas'); c.width = w; c.height = h;
            var ctx = c.getContext('2d'), img = ctx.createImageData(w, h), px = img.data;
            var bezel = Math.min(h * 0.42, 26);
            for (var y = 0; y < h; y++) {
                for (var x = 0; x < w; x++) {
                    // Jarak bertanda ke persegi bundar; arah pergeseran menunjuk ke dalam (kebalikan gradien jarak).
                    var qx = Math.abs(x + 0.5 - w / 2) - (w / 2 - r), qy = Math.abs(y + 0.5 - h / 2) - (h / 2 - r);
                    var ox = Math.max(qx, 0), oy = Math.max(qy, 0), len = Math.hypot(ox, oy);
                    var depth = -(len + Math.min(Math.max(qx, qy), 0) - r);
                    var i = (y * w + x) * 4, dx = 0, dy = 0;
                    if (depth < bezel) {
                        var t = 1 - Math.max(depth, 0) / bezel, kuat = t * t * (3 - 2 * t), nx, ny;
                        if (len > 0) { nx = ox / len * Math.sign(x + 0.5 - w / 2); ny = oy / len * Math.sign(y + 0.5 - h / 2); }
                        else if (qx > qy) { nx = Math.sign(x + 0.5 - w / 2); ny = 0; }
                        else { nx = 0; ny = Math.sign(y + 0.5 - h / 2); }
                        dx = -nx * kuat; dy = -ny * kuat;
                    }
                    px[i] = 128 + 127 * dx; px[i + 1] = 128 + 127 * dy; px[i + 2] = 128; px[i + 3] = 255;
                }
            }
            ctx.putImageData(img, 0, 0);
            peta.setAttribute('width', w); peta.setAttribute('height', h);
            peta.setAttribute('href', c.toDataURL());
            document.documentElement.classList.add('lens-ok');
        };
    };

    window.pkLensa = buatLensa;
    var gambar = buatLensa('lens-filter', bar, '::before', 2.5, 46);
    var timer;
    gambar();
    window.addEventListener('resize', function () { clearTimeout(timer); timer = setTimeout(gambar, 120); });
    window.addEventListener('load', gambar);
})();
