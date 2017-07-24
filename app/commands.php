<?php

use Chubbyphp\Lazy\LazyCommand;
use Chubbyphp\Model\Doctrine\DBAL\Command\CreateDatabaseCommand;
use Chubbyphp\Model\Doctrine\DBAL\Command\RunSqlCommand;
use Chubbyphp\Model\Doctrine\DBAL\Command\SchemaUpdateCommand;
use SlimSkeleton\Command\CreateUserCommand;
use SlimSkeleton\Provider\ConsoleProvider;
use SlimSkeleton\Repository\UserRepository;
use Slim\Container;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/* @var Container $container */
$container->register(new ConsoleProvider());

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
    return new SchemaUpdateCommand($container['db'], __DIR__.'/schema.php');
};

/* @var Container $container */
$container->extend('console.commands', function (array $commands) use ($container) {
    $commands[] = new LazyCommand(
        $container,
        CreateDatabaseCommand::class,
        'chubbyphp:model:dbal:database:create'
    );

    $commands[] = new LazyCommand(
        $container,
        RunSqlCommand::class,
        'chubbyphp:model:dbal:database:run:sql',
        [
            new InputArgument('sql', InputArgument::REQUIRED, 'The SQL statement to execute.'),
            new InputOption('depth', null, InputOption::VALUE_REQUIRED, 'Dumping depth of result set.', 7),
        ]
    );

    $commands[] = new LazyCommand(
        $container,
        SchemaUpdateCommand::class,
        'chubbyphp:model:dbal:database:schema:update',
        [
            new InputOption('dump', null, InputOption::VALUE_NONE, 'Dumps the generated SQL statements'),
            new InputOption('force', 'f', InputOption::VALUE_NONE, 'Executes the generated SQL statements.'),
        ]
    );

    $commands[] = new LazyCommand(
        $container,
        CreateUserCommand::class,
        'slim-skeleton:user:create',
        [
            new InputArgument('email', InputArgument::REQUIRED, 'The email address of the user.'),
            new InputArgument('password', InputArgument::REQUIRED, 'The password of the user.'),
            new InputArgument('roles', InputArgument::IS_ARRAY | InputArgument::REQUIRED, 'The roles of the user.'),
        ]
    );

    return $commands;
});
