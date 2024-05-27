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
    $routes->get('logout', 'AuthController::logout', ['filter' => 'universalauth']);
    $routes->get('invalid', 'AuthController::invalid');

    $routes->group('admin', ['namespace' => 'App\Controllers\Api\Admin'], function($routes) {
        $routes->post('register', 'AdminController::userRegister', ['filter' => 'adminauth']);
        $routes->get('invalid', 'AdminController::invalid');
    });

    $routes->group('user', ['namespace' => 'App\Controllers\Api\User'], function($routes) {
        $routes->post('register', 'AdminController::userRegister', ['filter' => 'adminauth']);
    });
    
});
