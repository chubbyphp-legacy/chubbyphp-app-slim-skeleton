<?php

use Slim\Container;
use SlimSkeleton\Controller\AuthController;
use SlimSkeleton\Controller\HomeController;
use SlimSkeleton\Controller\UserController;
use SlimSkeleton\Auth\AuthMiddleware;
use SlimSkeleton\Provider\ConsoleProvider;
use SlimSkeleton\Provider\DoctrineServiceProvider;
use SlimSkeleton\Provider\TwigProvider;
use SlimSkeleton\Repository\UserRepository;
use SlimSkeleton\Auth\Auth;

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

$container[AuthController::class] = function () use ($container) {
    return new AuthController(
        $container[Auth::class],
        $container['router']
    );
};

$container[UserController::class] = function () use ($container) {
    return new UserController(
        $container[Auth::class],
        $container['router'],
        $container['twig'],
        $container[UserRepository::class]
    );
};

// middlewares
$container[AuthMiddleware::class] = function () use ($container) {
    return new AuthMiddleware($container[Auth::class]);
};

// repositories
$container[UserRepository::class] = function () use ($container) {
    return new UserRepository($container['db']);
};

// services
$container[Auth::class] = function () use ($container) {
    return new Auth($container[UserRepository::class]);
};
