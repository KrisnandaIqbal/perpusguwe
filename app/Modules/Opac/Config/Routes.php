<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}

$routes->group('opac', ['namespace' => 'Opac\Controllers'], function ($subroutes) {
	$subroutes->add('', 'Opac::index');
	$subroutes->add('index', 'Opac::index');
	$subroutes->add('statistics', 'Opac::statistics');
	$subroutes->add('statistics_anggota', 'Opac::statistics_anggota');
	$subroutes->add('browse', 'Opac::browse');
	$subroutes->add('ddc', 'Opac::ddc');
	$subroutes->add('ddc/(:any)', 'Opac::ddc/$1');
	$subroutes->add('subjek', 'Opac::subjek');
	$subroutes->add('subjek/(:any)', 'Opac::subjek/$1');
	$subroutes->add('detail/(:any)', 'Opac::detail/$1');
	$subroutes->add('baca-digital/(:num)', 'Opac::bacaDigital/$1');
	$subroutes->add('visitor_export', 'Opac::visitor_export');
	$subroutes->add('member', 'Opac::member');
	$subroutes->add('member_export', 'Opac::member_export');

	// Login anggota (L) dan izin personalisasi (T8)
	$subroutes->get('masuk', 'Anggota::masuk');
	$subroutes->post('masuk', 'Anggota::prosesMasuk');
	$subroutes->get('keluar', 'Anggota::keluar');
	$subroutes->get('profil', 'Anggota::profil');
	$subroutes->post('profil/izin', 'Anggota::simpanIzin');
	
	// MARC Download Routes - Method terpisah untuk setiap format
	$subroutes->add('downloadMarcUtf8/(:num)', 'Opac::downloadMarcUtf8/$1');
	$subroutes->add('downloadMarcXml/(:num)', 'Opac::downloadMarcXml/$1');
	$subroutes->add('downloadMarcMods/(:num)', 'Opac::downloadMarcMods/$1');
	$subroutes->add('downloadMarcRdf/(:num)', 'Opac::downloadMarcRdf/$1');
	$subroutes->add('downloadMarcOai/(:num)', 'Opac::downloadMarcOai/$1');
	$subroutes->add('downloadMarcSrw/(:num)', 'Opac::downloadMarcSrw/$1');
});

$routes->group('api/Opac', ['namespace' => 'Opac\Controllers\Api'], function ($subroutes) {//crud
	$subroutes->add('', 'Opac::index');
	$subroutes->add('index', 'Opac::index');

	//custom
	$subroutes->add('click', 'Opac::click');
	$subroutes->add('visitor_datatable', 'Opac::visitor_datatable');
	$subroutes->add('visitor_datatable/(:any)/(:any)', 'Opac::visitor_datatable/$1/$2');
	$subroutes->add('member_datatable', 'Opac::member_datatable');
	$subroutes->add('member_datatable/(:any)', 'Opac::member_datatable/$1');
});