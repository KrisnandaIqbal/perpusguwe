<?php

if (!isset($routes)) {
    $routes = \Config\Services::routes(true);
}

$routes->group('laporan-email', ['namespace' => 'LaporanEmail\Controllers'], function ($subroutes) {
    $subroutes->add('', 'LaporanEmail::index');
    $subroutes->add('index', 'LaporanEmail::index');
    $subroutes->add('preview', 'LaporanEmail::preview');
    $subroutes->add('export', 'LaporanEmail::export');
    $subroutes->add('export_pdf', 'LaporanEmail::exportPdf');
});
