<?php

use Slim\Container;
use SlimSkeleton\Command\CreateDatabaseCommand;
use SlimSkeleton\Command\CreateUserCommand;
use SlimSkeleton\Command\LazyCommand;
use SlimSkeleton\Command\RunSqlCommand;
use SlimSkeleton\Command\SchemaUpdateCommand;
use SlimSkeleton\Provider\ConsoleProvider;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

$container->register(new ConsoleProvider());

/* @var Container $container */
$container->extend('console.commands', function (array $commands) use ($container) {
    $commands[] = new LazyCommand(
        $container,
        CreateDatabaseCommand::class,
        'slim-skeleton:database:create'
    );

    $commands[] = new LazyCommand(
        $container,
        RunSqlCommand::class,
        'slim-skeleton:database:run:sql',
        [
            new InputArgument('sql', InputArgument::REQUIRED, 'The SQL statement to execute.'),
            new InputOption('depth', null, InputOption::VALUE_REQUIRED, 'Dumping depth of result set.', 7),
        ]
    );

    $commands[] = new LazyCommand(
        $container,
        SchemaUpdateCommand::class,
        'slim-skeleton:database:schema:update',
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
