<?php if (!isset($routes)) {
        $routes = \Config\Services::routes(true);
}
$routes->group('laporan-cetak-label', ['namespace' => 'LaporanCetakLabel\Controllers'], function ($subroutes) {
        $subroutes->add('', 'LaporanCetakLabel::index');
        $subroutes->add('index', 'LaporanCetakLabel::index');
        $subroutes->add('export', 'LaporanCetakLabel::export');
});
