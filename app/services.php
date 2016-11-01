<?php

use Chubbyphp\Csrf\CsrfProvider;
use Chubbyphp\Session\SessionProvider;
use Chubbyphp\ErrorHandler\Slim\SimpleErrorHandlerProvider;
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
use Slim\Handlers\Error;
use SlimSkeleton\ErrorHandler\HtmlErrorResponseProvider;
use SlimSkeleton\Command\CreateUserCommand;
use SlimSkeleton\Command\LazyCommand;
use SlimSkeleton\Command\RunSqlCommand;
use SlimSkeleton\Command\SchemaDumpUpdateCommand;
use SlimSkeleton\Command\SchemaUpdateCommand;
use SlimSkeleton\Controller\AuthController;
use SlimSkeleton\Controller\HomeController;
use SlimSkeleton\Controller\UserController;
use SlimSkeleton\Middleware\LocaleMiddleware;
use SlimSkeleton\Provider\ConsoleProvider;
use SlimSkeleton\Provider\TwigProvider;
use SlimSkeleton\Repository\UserRepository;
use SlimSkeleton\Service\RedirectForPath;
use SlimSkeleton\Service\TemplateData;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/* @var Container $container */
$container->register(new AuthenticationProvider());
$container->register(new AuthorizationProvider());
$container->register(new ConsoleProvider());
$container->register(new CsrfProvider());
$container->register(new DoctrineServiceProvider());
$container->register(new MonologServiceProvider());
$container->register(new SessionProvider());
$container->register(new SimpleErrorHandlerProvider());
$container->register(new TranslationProvider());
$container->register(new TwigProvider());
$container->register(new ValidationProvider());

// extend providers
$container['errorHandler.defaultProvider'] = function () use ($container) {
    return $container[HtmlErrorResponseProvider::class];
};

$container->extend('console.commands', function (array $commands) use ($container) {
    $commands[] = $container['console.command.database.run.sql'];
    $commands[] = $container['console.command.database.schema.dump.update'];
    $commands[] = $container['console.command.database.schema.update'];
    $commands[] = $container['console.command.user.create'];

    return $commands;
});

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
$container['console.command.user.create'] = function () use ($container) {
    return new LazyCommand(
        'slim-skeleton:user:create',
        'Create a new user.',
        [
            new InputArgument('email', InputArgument::REQUIRED, 'The email address of the user.'),
            new InputArgument('password', InputArgument::REQUIRED, 'The password of the user.'),
            new InputArgument('roles', InputArgument::IS_ARRAY | InputArgument::REQUIRED, 'The roles of the user.'),
        ],
        function (InputInterface $input, OutputInterface $output) use ($container) {
            $command = new CreateUserCommand(
                $container['security.authentication.passwordmanager'],
                $container[UserRepository::class],
                $container['validator']
            );

            return $command($input, $output);
        }
    );
};

$container['console.command.database.run.sql'] = function () use ($container) {
    return new LazyCommand(
        'slim-skeleton:database:run:sql',
        'Executes arbitrary SQL directly from the command line.',
        [
            new InputArgument('sql', InputArgument::REQUIRED, 'The SQL statement to execute.'),
            new InputOption('depth', null, InputOption::VALUE_REQUIRED, 'Dumping depth of result set.', 7),
        ],
        function (InputInterface $input, OutputInterface $output) use ($container) {
            $command = new RunSqlCommand($container['db']);

            return $command($input, $output);
        }
    );
};

$container['console.command.database.schema.dump.update'] = function () use ($container) {
    $schema = $container['appDir'].'/schema.php';

    return new LazyCommand(
        'slim-skeleton:database:schema:dump:update',
        sprintf('Dump the update the database schema based on schema at "%s".', $schema),
        [],
        function (InputInterface $input, OutputInterface $output) use ($container, $schema) {
            $command = new SchemaDumpUpdateCommand($container['db'], $schema);

            return $command($input, $output);
        }
    );
};

$container['console.command.database.schema.update'] = function () use ($container) {
    $schema = $container['appDir'].'/schema.php';

    return new LazyCommand(
        'slim-skeleton:database:schema:update',
        sprintf('Update the database schema based on schema at "%s".', $schema),
        [],
        function (InputInterface $input, OutputInterface $output) use ($container, $schema) {
            $command = new SchemaUpdateCommand($container['db'], $schema);

            return $command($input, $output);
        }
    );
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

// repositories
$container[UserRepository::class] = function () use ($container) {
    return new UserRepository($container['db']);
};

// services
$container[Error::class] = function ($container) {
    return new Error($container['settings']['displayErrorDetails']);
};

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
        $container[Error::class],
        $container[TemplateData::class],
        $container['twig']
    );
};

$container[LanguageNegotiator::class] = function () use ($container) {
    return new LanguageNegotiator();
};

$container[LocaleMiddleware::class] = function () use ($container) {
    return new LocaleMiddleware(
        $container[LanguageNegotiator::class],
        $container['localeFallback'],
        $container['locales']
    );
};

$container[RedirectForPath::class] = function () use ($container) {
    return new RedirectForPath($container['router']);
};

$container[RoleAuthorization::class] = function ($container) {
    return new RoleAuthorization($container['security.authorization.rolehierarchyresolver'], $container['logger']);
};

$container[TemplateData::class] = function () use ($container) {
    return new TemplateData($container['security.authentication'], $container['session']);
};

$container[Repository::class.'User'] = function () use ($container) {
    return new Repository($container[UserRepository::class]);
};
