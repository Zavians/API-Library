<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->get('/', 'Home::index');



service('auth')->routes($routes);

$routes->group('api', ['namespace' => 'App\Controllers\Api'], function($routes) {
    $routes->post('first-register', 'AuthController::firstRegister');
    $routes->post('login', 'AuthController::login');
    $routes->get('invalid', 'AuthController::invalid');

    $routes->group('admin', ['namespace' => 'App\Controllers\Api\Admin'], function($routes) {
        $routes->post('register', 'AdminController::userRegister', ['filter' => 'adminauth']);
        $routes->get('logout', 'AdminController::logout', ['filter' => 'adminauth']);
        $routes->put('ganti-password', 'AdminController::ubahPassword', ['filter' => 'adminauth']);
        $routes->put('ganti-username', 'AdminController::ubahUsername', ['filter' => 'adminauth']);
        $routes->put('ganti-email', 'AdminController::ubahEmail', ['filter' => 'adminauth']);
        $routes->get('invalid', 'AdminController::invalid');

        //Book Controller
        $routes->post('tambah-buku', 'BookController::addBuku', ['filter' => 'adminauth']);
        $routes->put('perbarui-buku/(:num)', 'BookController::updateBuku/$1', ['filter' => 'adminauth']);
        $routes->get('lihat-semua-buku', 'BookController::showAllBuku', ['filter' => 'adminauth']);
        $routes->get('lihat-buku/(:num)', 'BookController::showBukuById/$1', ['filter' => 'adminauth']);

        //Peminjaman Controller
        $routes->put('perbarui-pinjaman/(:num)', 'PeminjamanController::updatePeminjaman/$1', ['filter' => 'adminauth']);

    });

    $routes->group('user', ['namespace' => 'App\Controllers\Api\User'], function($routes) {
       //Ulasan Controller
       $routes->post('ulas-buku/(:num)', 'UlasanController::addUlasan/$1', ['filter' => 'userauth']);
       $routes->get('ulasan-buku/(:num)', 'UlasanController::showAllUlasan/$1', ['filter' => 'userauth']);
       $routes->put('ubah-ulasan-buku/(:num)/(:num)', 'UlasanController::updateUlasan/$1/$2', ['filter' => 'userauth']);
       $routes->delete('hapus-ulasan-buku/(:num)/(:num)', 'UlasanController::deleteUlasan/$1/$2', ['filter' => 'userauth']);

       //Peminjaman Controller
       $routes->post('pinjam-buku/(:num)', 'PeminjamanController::addPeminjaman/$1', ['filter' => 'userauth']);
       $routes->get('sejarah-pinjaman', 'PeminjamanController::showAllPeminjaman', ['filter' => 'userauth']);
       $routes->get('sejarah-pinjaman-pinjam', 'PeminjamanController::showPinjamPeminjaman', ['filter' => 'userauth']);
       $routes->get('sejarah-pinjaman-dikembalikan', 'PeminjamanController::showDikembalikanPeminjaman', ['filter' => 'userauth']);
        //User Controller
        $routes->put('ubah-username', 'UserController::ubahUsername', ['filter' => 'userauth']);
        $routes->put('ubah-email', 'UserController::ubahEmail', ['filter' => 'userauth']);
       $routes->get('invalid', 'PeminjamanController::invalid');
    });

    
});
