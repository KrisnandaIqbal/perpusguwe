<?php

$routes->group('home', ['namespace' => 'Registrasi\Controllers'], function ($routes) {
    // Override OPAC keanggotaan online route to point to our new module
    $routes->get('pendaftaran_online', 'Registrasi::index');
    $routes->post('pendaftaran_online/submit', 'Registrasi::submit');
    $routes->get('pendaftaran-online', 'Registrasi::index');
    $routes->post('pendaftaran-online/submit', 'Registrasi::submit');
});

$routes->group('registrasi', ['namespace' => 'Registrasi\Controllers'], function ($routes) {
    $routes->get('/', 'Registrasi::index');
    $routes->post('submit', 'Registrasi::submit');
    $routes->get('success', 'Registrasi::success');
});
