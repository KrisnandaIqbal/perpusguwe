<?php
// Loader layar penuh: logo Kemnaker dengan gerak "rakit".
// CSS dan JS sengaja inline supaya overlay sudah tergambar sebelum file aset lain selesai dimuat.
// Sementara memakai logo yang digambar ulang; ganti isi <svg> saat SVG resmi tersedia.
//
// Dua mode:
// - biasa : logo baru muncul kalau halaman butuh lebih dari 300 ms, hilang saat DOM siap.
// - splash: kirim `loader_splash = true` dari controller (dipakai beranda). Logo langsung tampil,
//           ditahan sampai semua aset termuat dan minimal 3 detik, sekali per sesi tab.
$loaderSplash = ! empty($loader_splash);
?>
<style>
    .pk-loader { position: fixed; inset: 0; z-index: 99999; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 48px; background: #fff; transition: opacity .5s ease, visibility .5s ease; }
    .pk-loader.pk-selesai { opacity: 0; visibility: hidden; pointer-events: none; }
    .pk-loader .pk-logo, .pk-loader .pk-track { opacity: 0; animation: pk-muncul .25s ease .3s forwards; }
    .pk-loader.pk-splash .pk-logo, .pk-loader.pk-splash .pk-track { opacity: 1; animation: none; }
    .pk-logo { width: 200px; height: 200px; overflow: visible; color: #17416c; }
    .pk-logo path, .pk-logo circle { fill: currentColor; }
    .pk-logo .a0 { --d: 0s; } .pk-logo .a1 { --d: .12s; } .pk-logo .a2 { --d: .24s; } .pk-logo .a3 { --d: .36s; }
    .pk-logo .dot { animation: pk-rk-dot 2.8s cubic-bezier(.65,0,.35,1) var(--d) infinite; }
    .pk-logo .short { animation: pk-rk-short 2.8s cubic-bezier(.65,0,.35,1) calc(var(--d) + .07s) infinite; }
    .pk-logo .bar { animation: pk-rk-bar 2.8s cubic-bezier(.65,0,.35,1) calc(var(--d) + .14s) infinite; }
    @keyframes pk-rk-dot { 0%, 40%, 100% { transform: none; opacity: 1; } 62%, 70% { transform: translate(-120px, -120px); opacity: 0; } }
    @keyframes pk-rk-short { 0%, 40%, 100% { transform: none; opacity: 1; } 62%, 70% { transform: translate(-170px, 0); opacity: 0; } }
    @keyframes pk-rk-bar { 0%, 40%, 100% { transform: none; opacity: 1; } 62%, 70% { transform: translate(0, -170px); opacity: 0; } }
    .pk-track { position: relative; overflow: hidden; width: 120px; height: 3px; border-radius: 2px; background: #dfe6ee; }
    .pk-track span { position: absolute; top: 0; bottom: 0; left: 0; width: 40%; border-radius: 2px; background: #17416c; animation: pk-slide 1.4s cubic-bezier(.65,0,.35,1) infinite; }
    @keyframes pk-slide { 0% { transform: translateX(-100%); } 100% { transform: translateX(250%); } }
    @keyframes pk-muncul { to { opacity: 1; } }
    @media (max-width: 600px) { .pk-logo { width: 140px; height: 140px; } .pk-loader { gap: 36px; } }
    @media (prefers-reduced-motion: reduce) { .pk-logo *, .pk-track span { animation: none !important; } }
</style>
<noscript><style>.pk-loader { display: none; }</style></noscript>

<div id="pk-loader" class="pk-loader" role="status" aria-label="Memuat halaman" data-splash="<?= $loaderSplash ? '1' : '0' ?>">
    <svg class="pk-logo" viewBox="-760 -760 1520 1520" aria-hidden="true">
        <g transform="rotate(45)">
            <?php foreach ([0, 90, 180, 270] as $i => $sudut): ?>
            <g transform="rotate(<?= $sudut ?>)">
                <g class="a<?= $i ?>">
                    <circle class="dot" cx="-430" cy="-430" r="135"></circle>
                    <path class="short" d="M-455 -265L-335 -265A40 40 0 0 1 -295 -225L-295 -55A40 40 0 0 1 -335 -15L-455 -15A120 120 0 0 1 -575 -135L-575 -145A120 120 0 0 1 -455 -265Z"></path>
                    <path class="bar" d="M-265 -455A120 120 0 0 1 -145 -575L-135 -575A120 120 0 0 1 -15 -455L-15 -55A40 40 0 0 1 -55 -15L-225 -15A40 40 0 0 1 -265 -55Z"></path>
                </g>
            </g>
            <?php endforeach; ?>
        </g>
    </svg>
    <div class="pk-track" aria-hidden="true"><span></span></div>
</div>

<script>
    (function () {
        var loader = document.getElementById('pk-loader');
        if (!loader) return;

        var splash = false;
        if (loader.getAttribute('data-splash') === '1') {
            // Splash cukup sekali per sesi tab; kunjungan berikutnya ke beranda pakai mode biasa.
            try {
                splash = !sessionStorage.getItem('pk-splash');
                sessionStorage.setItem('pk-splash', '1');
            } catch (e) {
                splash = true;
            }
        }
        if (splash) loader.classList.add('pk-splash');

        var mulai = Date.now();
        function selesai() {
            if (loader.classList.contains('pk-selesai')) return;
            var tunggu = splash ? Math.max(0, 3000 - (Date.now() - mulai)) : 0;
            setTimeout(function () {
                loader.classList.add('pk-selesai');
                setTimeout(function () { loader.remove(); }, 500);
            }, tunggu);
        }

        // Splash menunggu banner dan gambar; mode biasa cukup sampai DOM siap.
        if (splash) {
            if (document.readyState === 'complete') selesai();
            else window.addEventListener('load', selesai);
        } else if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', selesai);
        } else {
            selesai();
        }
        // Pengaman kalau ada aset atau skrip pihak ketiga yang macet.
        setTimeout(selesai, 8000);
    })();
</script>
