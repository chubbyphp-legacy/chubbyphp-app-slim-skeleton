<?php

use Chubbyphp\Csrf\CsrfProvider;
use Chubbyphp\Session\SessionProvider;
use Chubbyphp\ErrorHandler\Slim\SimpleErrorHandlerProvider;
use Chubbyphp\Translation\LocaleTranslationProvider;
use Chubbyphp\Security\Authentication\AuthenticationMiddleware;
use Chubbyphp\Security\Authentication\FormAuthentication;
use Chubbyphp\Translation\TranslationProvider;
use Chubbyphp\Translation\TranslationTwigExtension;
use Chubbyphp\Validation\ValidationProvider;
use Negotiation\LanguageNegotiator;
use Slim\Container;
use Slim\Handlers\Error;
use SlimSkeleton\ErrorHandler\HtmlErrorResponseProvider;
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
$container->register(new SimpleErrorHandlerProvider());
$container->register(new TranslationProvider());
$container->register(new SessionProvider());
$container->register(new TwigProvider());
$container->register(new ValidationProvider());

// extend providers
$container['errorHandler.defaultProvider'] = function () use ($container) {
    return $container[HtmlErrorResponseProvider::class];
};

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
    return new HomeController($container[FormAuthentication::class], $container['session'], $container['twig']);
};

$container[AuthController::class] = function () use ($container) {
    return new AuthController($container[FormAuthentication::class], $container['router'], $container['session']);
};

$container[UserController::class] = function () use ($container) {
    return new UserController(
        $container[FormAuthentication::class],
        $container['router'],
        $container['session'],
        $container['twig'],
        $container[UserRepository::class],
        $container['validator']
    );
};

// middlewares
$container[AuthenticationMiddleware::class] = function () use ($container) {
    return new AuthenticationMiddleware($container[FormAuthentication::class]);
};

// repositories
$container[UserRepository::class] = function () use ($container) {
    return new UserRepository($container['db']);
};

// services
$container[FormAuthentication::class] = function () use ($container) {
    return new FormAuthentication($container['session'], $container[UserRepository::class]);
};

$container[HtmlErrorResponseProvider::class] = function () use ($container) {
    return new HtmlErrorResponseProvider(
        $container[FormAuthentication::class],
        $container[Error::class],
        $container['session'],
        $container['twig']
    );
};

$container[Error::class] = function ($container) {
    return new Error($container['settings']['displayErrorDetails']);
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
