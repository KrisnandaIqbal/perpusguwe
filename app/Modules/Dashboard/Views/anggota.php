<?php helper('menu'); ?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Anggota</title>
    <link rel="icon" href="<?= base_url('assets/img/logo-inlislite-icon.png') ?>">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,500;0,9..144,700;1,9..144,400;1,9..144,600&family=JetBrains+Mono:wght@400;500;600&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/dashboard-app/app.css') . '?v=' . filemtime(FCPATH . 'assets/dashboard-app/app.css') ?>">
    <style>
        .admin-fab { position: fixed; left: 16px; bottom: 16px; z-index: 60; display: flex; align-items: center; gap: 8px; padding: 10px 16px; border: 0; border-radius: 12px; background: #0284c7; color: #fff; font: 700 13px 'Plus Jakarta Sans', sans-serif; cursor: pointer; }
        .admin-fab:hover { background: #0369a1; }
        .admin-scrim { position: fixed; inset: 0; z-index: 70; background: rgba(15, 23, 42, .45); display: none; }
        .admin-drawer { position: fixed; top: 0; left: 0; bottom: 0; z-index: 80; width: 300px; max-width: 88vw; background: #fff; border-right: 1px solid #e2e8f0; display: flex; flex-direction: column; transform: translateX(-100%); transition: transform .2s ease; font-family: 'Plus Jakarta Sans', sans-serif; }
        body.admin-open .admin-drawer { transform: none; }
        body.admin-open .admin-scrim { display: block; }
        .admin-drawer header { display: flex; align-items: center; justify-content: space-between; padding: 16px 18px; border-bottom: 1px solid #e2e8f0; font-weight: 800; color: #0f172a; }
        .admin-drawer header button { border: 0; background: none; font-size: 18px; cursor: pointer; color: #475569; }
        .admin-drawer nav { flex: 1; overflow-y: auto; padding: 10px; }
        .admin-drawer ul { list-style: none; margin: 0; padding: 0; }
        .admin-drawer .submenu-argon { display: none; padding-left: 14px; }
        .admin-drawer .has-submenu.open > .submenu-argon { display: block; }
        .admin-drawer li a { display: flex; align-items: center; gap: 10px; padding: 9px 12px; border-radius: 8px; color: #334155; font-size: 14px; font-weight: 600; text-decoration: none; }
        .admin-drawer li a:hover { background: #f1f5f9; }
        .admin-drawer li a.active { background: #f0f9ff; color: #0369a1; }
        .admin-drawer li a .caret { margin-left: auto; font-size: 11px; }
        .admin-drawer li a i:not(.fas):not(.fa):not(.caret) { display: none; }
        .admin-drawer .nav-label-argon { margin: 14px 12px 4px; font-size: 11px; font-weight: 800; letter-spacing: .06em; text-transform: uppercase; color: #64748b; }
        .admin-drawer footer { display: grid; gap: 8px; padding: 12px; border-top: 1px solid #e2e8f0; }
        .admin-drawer footer a, .admin-drawer footer button { display: flex; align-items: center; justify-content: center; gap: 8px; padding: 10px; border-radius: 10px; border: 1px solid #e2e8f0; background: #fff; color: #0f172a; font: 700 13px 'Plus Jakarta Sans', sans-serif; text-decoration: none; cursor: pointer; }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 antialiased selection:bg-sky-600 selection:text-white">
    <div id="root"></div>

    <button type="button" class="admin-fab" id="adminMenuOpen"><i class="fas fa-bars"></i> Menu</button>
    <div class="admin-scrim" id="adminMenuScrim"></div>
    <aside class="admin-drawer" aria-label="Menu">
        <header>
            <span>Menu</span>
            <button type="button" id="adminMenuClose" aria-label="Tutup menu"><i class="fas fa-times"></i></button>
        </header>
        <nav><?= display_menu_backend(0, 1, isset(user()->category) ? user()->category : 'admin'); ?></nav>
        <footer>
            <a href="<?= base_url('logout') ?>"><i class="fas fa-sign-out-alt"></i> Keluar</a>
        </footer>
    </aside>

    <script type="module" src="<?= base_url('assets/dashboard-app/app.js') . '?v=' . filemtime(FCPATH . 'assets/dashboard-app/app.js') ?>"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggle = function (open) { document.body.classList.toggle('admin-open', open); };
            document.getElementById('adminMenuOpen').addEventListener('click', function () { toggle(true); });
            document.getElementById('adminMenuClose').addEventListener('click', function () { toggle(false); });
            document.getElementById('adminMenuScrim').addEventListener('click', function () { toggle(false); });
            document.querySelectorAll('.admin-drawer .submenu-toggle').forEach(function (t) {
                t.addEventListener('click', function (e) {
                    e.preventDefault();
                    this.closest('.has-submenu').classList.toggle('open');
                });
            });
        });
    </script>
</body>
</html>
