<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Landing::index');

$routes->get('/home', 'Home::index');
$routes->post('/home/delete', 'Home::delete');

$routes->get('/menu', 'Menu::index');
$routes->post('/menu/add', 'Menu::add');
$routes->post('/menu/edit', 'Menu::edit');
$routes->post('/menu/copy', 'Menu::copy');

$routes->get('/settings', 'Settings::index');
$routes->post('/settings/add', 'Settings::add');
$routes->post('/settings/edit', 'Settings::edit');

$routes->get('/user', 'User::index');
$routes->post('/user/add', 'User::add');
$routes->post('/user/edit', 'User::edit');

$routes->get('/options', 'Options::index');
$routes->post('/options/add', 'Options::add');
$routes->post('/options/edit', 'Options::edit');

$routes->get('/barang', 'Barang::index');
$routes->post('/barang/add', 'Barang::add');
$routes->post('/barang/edit', 'Barang::edit');

$routes->get('/profile', 'Profile::index');
$routes->post('/profile/edit', 'Profile::edit');

$routes->get('/pengeluaran', 'Pengeluaran::index');
$routes->post('/pengeluaran/add', 'Pengeluaran::add');
$routes->post('/pengeluaran/edit', 'Pengeluaran::edit');
$routes->post('/pengeluaran/list', 'Pengeluaran::list');

$routes->get('/inv', 'Inv::index');
$routes->post('/inv/add', 'Inv::add');
$routes->post('/inv/edit', 'Inv::edit');
$routes->post('/inv/edit', 'Inv::edit');
$routes->post('/inv/list', 'Inv::list');

$routes->get('/toko', 'Toko::index');
$routes->post('/toko/add', 'Toko::add');
$routes->post('/toko/edit', 'Toko::edit');

$routes->get('/customer', 'Customer::index');
$routes->post('/customer/add', 'Customer::add');
$routes->post('/customer/edit', 'Customer::edit');

$routes->get('/transaksi', 'Transaksi::index');
$routes->post('/transaksi/bayar', 'Transaksi::bayar');
$routes->post('/transaksi/cari_user', 'Transaksi::cari_user');
$routes->post('/transaksi/cari_customer', 'Transaksi::cari_customer');
$routes->post('/transaksi/cari_barang', 'Transaksi::cari_barang');
$routes->post('/transaksi/list', 'Transaksi::list');

$routes->get('/hutang', 'Hutang::index');
$routes->post('/hutang/detail', 'Hutang::detail');
$routes->post('/hutang/wa', 'Hutang::wa');
$routes->post('/hutang/kasir', 'Hutang::kasir');
$routes->post('/hutang/bayar', 'Hutang::bayar');

$routes->get('/guest/nota/(:any)', 'Guest::nota/$1');
$routes->post('/guest/login', 'Guest::login');
$routes->get('/guest/logout', 'Guest::logout');
