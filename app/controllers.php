<?php

use Chubbyphp\Security\Authentication\FormAuthentication;
use SlimSkeleton\Controller\AuthController;

use SlimSkeleton\Controller\HomeController;
use SlimSkeleton\Controller\UserController;
use SlimSkeleton\ErrorHandler\ErrorResponseHandler;
use SlimSkeleton\Repository\UserRepository;
use SlimSkeleton\Service\RedirectForPath;
use SlimSkeleton\Service\TwigRender;
use Slim\App;
use Slim\Container;

/* @var App $app */
/* @var Container $container */

$container[AuthController::class] = function () use ($container) {
    return new AuthController(
        $container[FormAuthentication::class], // need cause login/logout
        $container[RedirectForPath::class],
        $container['session']
    );
};

$container[HomeController::class] = function () use ($container) {
    return new HomeController($container[TwigRender::class]);
};

$container[UserController::class] = function () use ($container) {
    return new UserController(
        $container['security.authentication'],
        $container['security.authorization'],
        $container['deserializer'],
        $container[ErrorResponseHandler::class],
        $container[RedirectForPath::class],
        $container['security.authorization.rolehierarchyresolver'],
        $container['session'],
        $container[TwigRender::class],
        $container[UserRepository::class],
        $container['validator']
    );
};

$app->group('/{locale:'.implode('|', $container['locales']).'}', function () use ($app, $container) {
    $app->get('', HomeController::class.':home')->setName('home');

    $app->post('/login', AuthController::class.':login')->setName('login');
    $app->post('/logout', AuthController::class.':logout')->setName('logout');

    $app->group('/users', function () use ($app, $container) {
        $app->get('', UserController::class.':listAll')->setName('user_list');
        $app->map(['GET', 'POST'], '/create', UserController::class.':create')->setName('user_create');
        $app->get('/{id}/read', UserController::class.':read')->setName('user_read');
        $app->map(['GET', 'POST'], '/{id}/update', UserController::class.':update')->setName('user_update');
        $app->post('/{id}/delete', UserController::class.':delete')->setName('user_delete');
    })->add($container['security.authentication.middleware']);
});
