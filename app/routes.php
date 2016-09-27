<?php

use Slim\App;
use Slim\Container;
use SlimSkeleton\Controller\AuthController;
use SlimSkeleton\Controller\HomeController;
use SlimSkeleton\Controller\UserController;
use SlimSkeleton\Auth\AuthMiddleware;

/* @var App $app */
/* @var Container $container */

$app->group('/{locale:' . implode('|', $container['locales']) .'}', function () use ($app, $container) {
    $app->get('', HomeController::class.':home')->setName('home');

    $app->post('/login', AuthController::class.':login')->setName('login');
    $app->post('/logout', AuthController::class.':logout')->setName('logout');

    $app->group('/users', function () use ($app, $container) {
        $app->get('', UserController::class.':listAll')->setName('user_list');
        $app->map(['GET', 'POST'], '/create', UserController::class.':create')->setName('user_create');
        $app->map(['GET', 'POST'], '/{id}/edit', UserController::class.':edit')->setName('user_edit');
        $app->get('/{id}/view', UserController::class.':view')->setName('user_view');
        $app->post('/{id}/delete', UserController::class.':delete')->setName('user_delete');
    })->add($container[AuthMiddleware::class]);
});
