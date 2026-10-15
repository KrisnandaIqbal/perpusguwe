<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}

// Akses langsung /unit-kerja maupun /master-unit-kerja
$routes->add('unit-kerja', 'UnitKerja::index', ['namespace' => 'UnitKerja\Controllers']);

$routes->group('master-unit-kerja', ['namespace' => 'UnitKerja\Controllers'], function ($subroutes) {
	$subroutes->add('', 'UnitKerja::index');
	$subroutes->add('index', 'UnitKerja::index');
	$subroutes->add('apply_status/(:any)', 'UnitKerja::apply_status/$1');
	$subroutes->add('apply_status_subunit/(:any)', 'UnitKerja::apply_status_subunit/$1');
	$subroutes->add('delete_subunit/(:any)', 'UnitKerja::delete_subunit/$1');
});

$routes->group('api/unit-kerja', ['namespace' => 'UnitKerja\Controllers\Api'], function ($subroutes) {
	// Sub-unit endpoints (CRUD, Datatable, Toggle, Delete)
	$subroutes->add('subunit/datatable', 'UnitKerja::datatable_subunit');
	$subroutes->add('subunit/detail/(:any)', 'UnitKerja::detail_subunit/$1');
	$subroutes->add('subunit/create', 'UnitKerja::create_subunit');
	$subroutes->add('subunit/edit/(:any)', 'UnitKerja::edit_subunit/$1');
	$subroutes->add('subunit/delete/(:any)', 'UnitKerja::delete_subunit/$1');
	$subroutes->add('sub-units/(:any)', 'UnitKerja::sub_units/$1');

	// Unit Kerja Induk endpoints
	$subroutes->add('datatable', 'UnitKerja::datatable');
	$subroutes->add('detail/(:any)', 'UnitKerja::detail/$1');
	$subroutes->add('edit/(:any)', 'UnitKerja::edit/$1');
});