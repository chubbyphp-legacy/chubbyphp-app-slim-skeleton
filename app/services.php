<?php

use Chubbyphp\Csrf\CsrfProvider;
use Chubbyphp\Session\SessionProvider;
use Chubbyphp\ErrorHandler\SimpleErrorHandlerProvider;
use Chubbyphp\Security\Authentication\AuthenticationProvider;
use Chubbyphp\Security\Authentication\FormAuthentication;
use Chubbyphp\Security\Authorization\AuthorizationProvider;
use Chubbyphp\Security\Authorization\RoleAuthorization;
use Chubbyphp\Translation\LocaleTranslationProvider;
use Chubbyphp\Translation\TranslationProvider;
use Chubbyphp\Translation\TranslationTwigExtension;
use Chubbyphp\Validation\Requirements\Repository;
use Chubbyphp\Validation\ValidationProvider;
use Negotiation\LanguageNegotiator;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\MonologServiceProvider;
use Slim\Container;
use SlimSkeleton\Command\CreateDatabaseCommand;
use SlimSkeleton\Command\CreateUserCommand;
use SlimSkeleton\Command\RunSqlCommand;
use SlimSkeleton\Command\SchemaUpdateCommand;
use SlimSkeleton\Controller\AuthController;
use SlimSkeleton\Controller\HomeController;
use SlimSkeleton\Controller\UserController;
use SlimSkeleton\ErrorHandler\HtmlErrorResponseProvider;
use SlimSkeleton\Middleware\LocaleMiddleware;
use SlimSkeleton\Provider\TwigProvider;
use SlimSkeleton\Repository\UserRepository;
use SlimSkeleton\Service\RedirectForPath;
use SlimSkeleton\Service\TemplateData;

/* @var Container $container */
$container->register(new AuthenticationProvider());
$container->register(new AuthorizationProvider());
$container->register(new CsrfProvider());
$container->register(new DoctrineServiceProvider());
$container->register(new SimpleErrorHandlerProvider());
$container->register(new MonologServiceProvider());
$container->register(new SessionProvider());
$container->register(new TranslationProvider());
$container->register(new TwigProvider());
$container->register(new ValidationProvider());

// extend providers
$container['errorHandler.defaultProvider'] = function () use ($container) {
    return $container[HtmlErrorResponseProvider::class];
};

$container->extend('security.authentication.authentications', function (array $authentications) use ($container) {
    $authentications[] = $container[FormAuthentication::class];

    return $authentications;
});

$container->extend('security.authorization.authorizations', function (array $authorizations) use ($container) {
    $authorizations[] = $container[RoleAuthorization::class];

    return $authorizations;
});

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

$container->extend('validator.helpers', function (array $helpers) use ($container) {
    $helpers[] = $container[Repository::class.'User'];

    return $helpers;
});

// commands
$container[CreateDatabaseCommand::class] = function () use ($container) {
    return new CreateDatabaseCommand($container['db']);
};

$container[CreateUserCommand::class] = function () use ($container) {
    return new CreateUserCommand(
        $container['security.authentication.passwordmanager'],
        $container[UserRepository::class],
        $container['validator']
    );
};

$container[RunSqlCommand::class] = function () use ($container) {
    return new RunSqlCommand($container['db']);
};

$container[SchemaUpdateCommand::class] = function () use ($container) {
    return new SchemaUpdateCommand($container['db'], $container['appDir'].'/schema.php');
};

// controllers
$container[HomeController::class] = function () use ($container) {
    return new HomeController($container[TemplateData::class], $container['twig']);
};

$container[AuthController::class] = function () use ($container) {
    return new AuthController(
        $container[FormAuthentication::class], // need cause login/logout
        $container[RedirectForPath::class],
        $container['session']
    );
};

$container[UserController::class] = function () use ($container) {
    return new UserController(
        $container['security.authentication'],
        $container['security.authorization'],
        $container['security.authentication.passwordmanager'],
        $container[RedirectForPath::class],
        $container['security.authorization.rolehierarchyresolver'],
        $container['session'],
        $container[TemplateData::class],
        $container['twig'],
        $container[UserRepository::class],
        $container['validator']
    );
};

// middlewares
$container[LocaleMiddleware::class] = function () use ($container) {
    return new LocaleMiddleware(
        $container[LanguageNegotiator::class],
        $container['localeFallback'],
        $container['locales']
    );
};

// repositories
$container[UserRepository::class] = function () use ($container) {
    return new UserRepository($container['db'], $container['logger']);
};

// services
$container[FormAuthentication::class] = function ($container) {
    return new FormAuthentication(
        $container['security.authentication.passwordmanager'],
        $container['session'],
        $container[UserRepository::class],
        $container['logger']
    );
};

$container[HtmlErrorResponseProvider::class] = function () use ($container) {
    return new HtmlErrorResponseProvider(
        $container['errorHandler'],
        $container[TemplateData::class],
        $container['twig']
    );
};

$container[LanguageNegotiator::class] = function () use ($container) {
    return new LanguageNegotiator();
};

$container[RedirectForPath::class] = function () use ($container) {
    return new RedirectForPath($container['router']);
};

$container[Repository::class.'User'] = function () use ($container) {
    return new Repository($container[UserRepository::class]);
};

$container[RoleAuthorization::class] = function ($container) {
    return new RoleAuthorization($container['security.authorization.rolehierarchyresolver'], $container['logger']);
};

$container[TemplateData::class] = function () use ($container) {
    return new TemplateData($container['security.authentication'], $container['debug'], $container['session']);
};
