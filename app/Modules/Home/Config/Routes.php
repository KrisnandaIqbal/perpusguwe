<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
// Update your existing routes to include recommendations

$routes->group('home', ['namespace' => 'Home\Controllers'], function ($subroutes) {
	$subroutes->add('', 'Home::index');
	$subroutes->add('index', 'Home::index');
	$subroutes->add('pendaftaran-online', 'Home::pendaftaran_online');
	$subroutes->add('tata-cara-sirkulasi', 'Home::tata_cara_sirkulasi');
	$subroutes->add('layanan-isbn', 'Home::layanan_isbn');
	$subroutes->add('search-books', 'Home::searchBooks');

});

$routes->group('api/home', ['namespace' => 'Home\Controllers\Api'], function ($subroutes) {//crud
	$subroutes->add('', 'Home::index');
	$subroutes->add('index', 'Home::index');
	$subroutes->add('pendaftaran_online', 'Home::pendaftaran_online');
	$subroutes->add('index', 'Home::index');

	
});