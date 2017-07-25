<?php

use Chubbyphp\Csrf\CsrfProvider;
use Chubbyphp\Deserialization\Mapping\LazyObjectMapping as DeserializationLazyObjectMapping;
use Chubbyphp\Deserialization\Provider\DeserializationProvider;
use Chubbyphp\Model\StorageCache\ArrayStorageCache;
use Chubbyphp\Model\Resolver;
use Chubbyphp\Security\Authentication\AuthenticationProvider;
use Chubbyphp\Security\Authentication\FormAuthentication;
use Chubbyphp\Security\Authorization\AuthorizationProvider;
use Chubbyphp\Security\Authorization\RoleAuthorization;
use Chubbyphp\Session\SessionProvider;
use Chubbyphp\Translation\LocaleTranslationProvider;
use Chubbyphp\Translation\TranslationProvider;
use Chubbyphp\Translation\TranslationTwigExtension;
use Chubbyphp\Validation\Mapping\LazyObjectMapping as ValidationLazyObjectMapping;
use Chubbyphp\Validation\Provider\ValidationProvider;
use SlimSkeleton\Csrf\CsrfErrorHandler;
use SlimSkeleton\Deserialization\UserMapping as DeserializationUserMapping;
use SlimSkeleton\Deserialization\UserSearchMapping as DeserializationUserSearchMapping;
use SlimSkeleton\ErrorHandler\ErrorResponseHandler;
use SlimSkeleton\Model\User;
use SlimSkeleton\Provider\TwigProvider;
use SlimSkeleton\Repository\UserRepository;
use SlimSkeleton\Search\UserSearch;
use SlimSkeleton\Security\AuthenticationErrorHandler;
use SlimSkeleton\Service\RedirectForPath;
use SlimSkeleton\Service\TwigRender;
use SlimSkeleton\Twig\NumericExtension;
use SlimSkeleton\Twig\RouterExtension;
use SlimSkeleton\Validation\UserMapping as ValidationUserMapping;
use SlimSkeleton\Validation\UserSearchMapping as ValidationUserSearchMapping;
use Negotiation\LanguageNegotiator;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\MonologServiceProvider;
use Slim\Container;

/* @var Container $container */
$container->register(new AuthenticationProvider());
$container->register(new AuthorizationProvider());
$container->register(new CsrfProvider());
$container->register(new DeserializationProvider());
$container->register(new DoctrineServiceProvider());
$container->register(new MonologServiceProvider());
$container->register(new SessionProvider());
$container->register(new TranslationProvider());
$container->register(new TwigProvider());
$container->register(new ValidationProvider());

// extend providers
$container['security.authentication.errorResponseHandler'] = function () use ($container) {
    return new AuthenticationErrorHandler($container[ErrorResponseHandler::class]);
};

$container['csrf.errorResponseHandler'] = function () use ($container) {
    return new CsrfErrorHandler($container['session']);
};

$container['deserializer.emptystringtonull'] = true;

$container->extend('deserializer.objectmappings', function (array $objectMappings) use ($container) {
    $objectMappings[] = new DeserializationLazyObjectMapping(
        $container,
        DeserializationUserMapping::class,
        User::class
    );
    $objectMappings[] = new DeserializationLazyObjectMapping(
        $container,
        DeserializationUserSearchMapping::class,
        UserSearch::class
    );

    return $objectMappings;
});

$container->extend('security.authentication.authentications', function (array $authentications) use ($container) {
    $authentications[] = $container[FormAuthentication::class];

    return $authentications;
});

$container->extend('security.authorization.authorizations', function (array $authorizations) use ($container) {
    $authorizations[] = $container[RoleAuthorization::class];

    return $authorizations;
});

$container->extend('security.authorization.rolehierarchy', function (array $rolehierarchy) use ($container) {
    $rolehierarchy['ADMIN'] = ['USER'];
    $rolehierarchy['USER'] = [];

    return $rolehierarchy;
});

$container->extend('translator.providers', function (array $providers) use ($container) {
    $providers[] = new LocaleTranslationProvider(
        'de',
        array_replace(
            require $container['vendorDir'].'/chubbyphp/chubbyphp-validation/translations/de.php',
            require $container['vendorDir'].'/chubbyphp/chubbyphp-validation-model/translations/de.php',
            require $container['translationDir'].'/de.php'
        )
    );
    $providers[] = new LocaleTranslationProvider(
        'en',
        array_replace(
            require $container['vendorDir'].'/chubbyphp/chubbyphp-validation/translations/en.php',
            require $container['vendorDir'].'/chubbyphp/chubbyphp-validation-model/translations/en.php',
            require $container['translationDir'].'/en.php'
        )
    );

    return $providers;
});

$container->extend('twig.namespaces', function (array $namespaces) use ($container) {
    $namespaces['SlimSkeleton'] = $container['viewDir'];

    return $namespaces;
});

$container->extend('twig.extensions', function (array $extensions) use ($container) {
    $extensions[] = new NumericExtension();
    $extensions[] = new RouterExtension($container['router']);
    $extensions[] = new TranslationTwigExtension($container['translator']);
    if ($container['debug']) {
        $extensions[] = new \Twig_Extension_Debug();
    }

    return $extensions;
});

$container->extend('validator.objectmappings', function (array $objectMappings) use ($container) {
    $objectMappings[] = new ValidationLazyObjectMapping(
        $container,
        ValidationUserMapping::class,
        User::class
    );
    $objectMappings[] = new ValidationLazyObjectMapping(
        $container,
        ValidationUserSearchMapping::class,
        UserSearch::class
    );

    return $objectMappings;
});

// deserializer
$container[DeserializationUserMapping::class] = function () use ($container) {
    return new DeserializationUserMapping(
        $container['security.authentication.passwordmanager'],
        $container['security.authorization.rolehierarchyresolver']
    );
};

$container[DeserializationUserSearchMapping::class] = function () use ($container) {
    return new DeserializationUserSearchMapping();
};

// repositories
$container[ArrayStorageCache::class] = function () {
    return new ArrayStorageCache();
};

$container[UserRepository::class] = function () use ($container) {
    return new UserRepository(
        $container['db'],
        $container[Resolver::class],
        $container[ArrayStorageCache::class],
        $container['logger']
    );
};

$container[Resolver::class] = function () use ($container) {
    return new Resolver($container, [
        UserRepository::class,
    ]);
};

// services
$container[ErrorResponseHandler::class] = function () use ($container) {
    return new ErrorResponseHandler($container[TwigRender::class]);
};

$container[FormAuthentication::class] = function ($container) {
    return new FormAuthentication(
        $container['security.authentication.passwordmanager'],
        $container['session'],
        $container[UserRepository::class],
        $container['logger']
    );
};

$container[LanguageNegotiator::class] = function () use ($container) {
    return new LanguageNegotiator();
};

$container[RedirectForPath::class] = function () use ($container) {
    return new RedirectForPath($container['router']);
};

$container[RoleAuthorization::class] = function ($container) {
    return new RoleAuthorization($container['security.authorization.rolehierarchyresolver'], $container['logger']);
};

$container[TwigRender::class] = function () use ($container) {
    return new TwigRender(
        $container['security.authentication'],
        $container['debug'],
        $container['session'],
        [
            'user_create' => ['user_list'],
            'user_delete' => ['user_list'],
            'user_update' => ['user_list'],
            'user_list' => [],
            'user_read' => ['user_list'],
        ],
        $container['translator'],
        $container['twig']
    );
};

// validation
$container[ValidationUserMapping::class] = function () use ($container) {
    return new ValidationUserMapping($container[Resolver::class]);
};

$container[ValidationUserSearchMapping::class] = function () use ($container) {
    return new ValidationUserSearchMapping();
};
