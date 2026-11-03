<?php
if (!isset($routes)) {
    $routes = \Config\Services::routes(true);
}

$routes->group('cetak-label', ['namespace' => 'CetakLabel\Controllers'], function ($subroutes) {
    $subroutes->add('',            'CetakLabelController::index');
    $subroutes->add('index',       'CetakLabelController::index');
    $subroutes->add('print_label', 'CetakLabelController::print_label');
    $subroutes->add('status',      'CetakLabelController::status');
});