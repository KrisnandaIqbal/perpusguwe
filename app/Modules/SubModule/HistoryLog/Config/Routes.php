<?php if (!isset($routes)) {
    $routes = \Config\Services::routes(true);
}
$routes->group('riwayat-perubahan', ['namespace' => 'HistoryLog\Controllers'], function ($subroutes) {
    $subroutes->add('', 'HistoryLog::index');
    $subroutes->add('index', 'HistoryLog::index');
});