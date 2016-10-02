<?php

use Chubbyphp\Csrf\CsrfProvider;
use Chubbyphp\Session\SessionProvider;
use Chubbyphp\Translation\LocaleTranslationProvider;
use Chubbyphp\Translation\TranslationProvider;
use Chubbyphp\Translation\TranslationTwigExtension;
use Chubbyphp\Validation\ValidationProvider;
use Negotiation\LanguageNegotiator;
use Slim\Container;
use SlimSkeleton\Error\ErrorHandler;
use SlimSkeleton\Security\Auth;
use SlimSkeleton\Security\AuthMiddleware;
use SlimSkeleton\Controller\AuthController;
use SlimSkeleton\Controller\HomeController;
use SlimSkeleton\Controller\UserController;
use SlimSkeleton\Middleware\LocaleMiddleware;
use SlimSkeleton\Provider\ConsoleProvider;
use SlimSkeleton\Provider\DoctrineServiceProvider;
use SlimSkeleton\Provider\TwigProvider;
use SlimSkeleton\Repository\UserRepository;

/* @var Container $container */
$container->register(new ConsoleProvider());
$container->register(new CsrfProvider());
$container->register(new DoctrineServiceProvider());
$container->register(new TranslationProvider());
$container->register(new SessionProvider());
$container->register(new TwigProvider());
$container->register(new ValidationProvider());

// extend providers
$container['csrf.errorHandler.key'] = ErrorHandler::class;

$container->extend('translator.providers', function (array $providers) use ($container) {
    $translationDir = $container['appDir'].'/Resources/translations';
    $providers[] = new LocaleTranslationProvider('de', require $translationDir.'/de.php');
    $providers[] = new LocaleTranslationProvider('en', require $translationDir.'/en.php');

    return $providers;
});

$container->extend('twig.namespaces', function (array $namespaces) use ($container) {
    $namespaces['SlimSkeleton'] = $container['appDir'].'/Resources/views';

    return $namespaces;
});

$container->extend('twig.extensions', function (array $extensions) use ($container) {
    $extensions[] = new TranslationTwigExtension($container['translator']);
    if ($container['debug']) {
        $extensions[] = new \Twig_Extension_Debug();
    }

    return $extensions;
});

$container->extend('validator.repositories', function (array $repositories) use ($container) {
    $repositories[] = $container[UserRepository::class];

    return $repositories;
});

// controllers
$container[HomeController::class] = function () use ($container) {
    return new HomeController($container[Auth::class], $container['session'], $container['twig']);
};

$container[AuthController::class] = function () use ($container) {
    return new AuthController($container[Auth::class], $container['router'], $container['session']);
};

$container[UserController::class] = function () use ($container) {
    return new UserController(
        $container[Auth::class],
        $container[ErrorHandler::class],
        $container['router'],
        $container['session'],
        $container['twig'],
        $container[UserRepository::class],
        $container['validator']
    );
};

// middlewares
$container[AuthMiddleware::class] = function () use ($container) {
    return new AuthMiddleware($container[Auth::class], $container[ErrorHandler::class]);
};

// repositories
$container[UserRepository::class] = function () use ($container) {
    return new UserRepository($container['db']);
};

// services
$container[Auth::class] = function () use ($container) {
    return new Auth($container['session'], $container[UserRepository::class]);
};

$container[ErrorHandler::class] = function () use ($container) {
    return new ErrorHandler($container[Auth::class], $container['session'], $container['twig']);
};

$container[LocaleMiddleware::class] = function () use ($container) {
    return new LocaleMiddleware(
        $container[LanguageNegotiator::class],
        $container['localeFallback'],
        $container['locales']
    );
};

$container[LanguageNegotiator::class] = function () use ($container) {
    return new LanguageNegotiator();
};
