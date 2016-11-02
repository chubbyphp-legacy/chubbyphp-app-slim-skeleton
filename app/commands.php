<?php

use Slim\Container;
use SlimSkeleton\Command\CreateUserCommand;
use SlimSkeleton\Command\LazyCommand;
use SlimSkeleton\Command\RunSqlCommand;
use SlimSkeleton\Command\SchemaUpdateCommand;
use SlimSkeleton\Provider\ConsoleProvider;
use SlimSkeleton\Repository\UserRepository;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

$container->register(new ConsoleProvider());

/* @var Container $container */
$container->extend('console.commands', function (array $commands) use ($container) {
    $commands[] = $container['console.command.database.run.sql'];
    $commands[] = $container['console.command.database.schema.update'];
    $commands[] = $container['console.command.user.create'];

    return $commands;
});

$container['console.command.user.create'] = function () use ($container) {
    return new LazyCommand(
        'slim-skeleton:user:create',
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

$container['console.command.database.schema.update'] = function () use ($container) {
    return new LazyCommand(
        'slim-skeleton:database:schema:update',
        [
            new InputOption(
                'dump-sql', null, InputOption::VALUE_NONE,
                'Dumps the generated SQL statements to the screen (does not execute them).'
            ),
            new InputOption(
                'force', 'f', InputOption::VALUE_NONE,
                'Causes the generated SQL statements to be physically executed against your database.'
            ),
        ],
        function (InputInterface $input, OutputInterface $output) use ($container) {
            $command = new SchemaUpdateCommand($container['db'], $container['appDir'].'/schema.php');

            return $command($input, $output);
        }
    );
};
