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
        $routes->get('invalid', 'AdminController::invalid');

        //Book Controller
        $routes->post('tambah-buku', 'BookController::addBuku', ['filter' => 'adminauth']);
        $routes->put('perbarui-buku/(:num)', 'BookController::updateBuku/$1', ['filter' => 'adminauth']);
        $routes->get('lihat-semua-buku', 'BookController::showAllBuku', ['filter' => 'adminauth']);
        $routes->get('lihat-buku/(:num)', 'BookController::showBukuById/$1', ['filter' => 'adminauth']);
    });

    $routes->group('user', ['namespace' => 'App\Controllers\Api\User'], function($routes) {
       //Ulasan Controller
       $routes->post('ulas-buku/(:num)', 'UlasanController::addUlasan/$1', ['filter' => 'userauth']);
       $routes->get('ulasan-buku/(:num)', 'UlasanController::showAllUlasan/$1', ['filter' => 'userauth']);
       $routes->put('ubah-ulasan-buku/(:num)/(:num)', 'UlasanController::updateUlasan/$1/$2', ['filter' => 'userauth']);
       $routes->post('hapus-ulasan-buku/(:num)/(:num)', 'UlasanController::deleteUlasan/$1/$2', ['filter' => 'userauth']);

       //Peminjaman Controller

    });

    
});
