<?php

use Slim\Container;
use SlimSkeleton\Controller\HomeController;
use SlimSkeleton\Controller\UserController;
use SlimSkeleton\Middleware\AuthMiddleware;
use SlimSkeleton\Provider\ConsoleProvider;
use SlimSkeleton\Provider\DoctrineServiceProvider;
use SlimSkeleton\Provider\TwigProvider;
use SlimSkeleton\Repository\UserRepository;

/* @var Container $container */

$container->register(new ConsoleProvider());
$container->register(new DoctrineServiceProvider());
$container->register(new TwigProvider());

$container->extend('twig.namespaces', function (array $namespaces) use ($container) {
    $namespaces['SlimSkeleton'] = $container['appDir'].'/Resources/views';

    return $namespaces;
});

// controllers
$container[HomeController::class] = function () use ($container) {
    return new HomeController($container['twig']);
};

$container[UserController::class] = function () use ($container) {
    return new UserController(
        $container[UserRepository::class],
        $container['twig']
    );
};

// middlewares
$container[AuthMiddleware::class] = function () {
    return new AuthMiddleware();
};

// repositories
$container[UserRepository::class] = function () use ($container) {
    return new UserRepository($container['db']);
};
