<?php

namespace SlimSkeleton\Command;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * This file is part of the Doctrine Bundle.
 *
 * The code was originally distributed inside the Symfony framework.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 * (c) Doctrine Project, Benjamin Eberlei <kontakt@beberlei.de>
 */
final class CreateDatabaseCommand
{
    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null
     */
    public function __invoke(InputInterface $input, OutputInterface $output)
    {
        $connection = $this->connection;
        $ifNotExists = $input->getOption('if-not-exists');
        $params = $connection->getParams();

        if (isset($params['master'])) {
            $params = $params['master'];
        }

        // Cannot inject `shard` option in parent::getDoctrineConnection
        // cause it will try to connect to a non-existing database
        if (isset($params['shards'])) {
            $shards = $params['shards'];
            // Default select global
            $params = array_merge($params, $params['global']);
            unset($params['global']['dbname']);
            if ($input->getOption('shard')) {
                foreach ($shards as $i => $shard) {
                    if ($shard['id'] === (int) $input->getOption('shard')) {
                        // Select sharded database
                        $params = array_merge($params, $shard);
                        unset($params['shards'][$i]['dbname'], $params['id']);
                        break;
                    }
                }
            }
        }

        $hasPath = isset($params['path']);
        $name = $hasPath ? $params['path'] : (isset($params['dbname']) ? $params['dbname'] : false);

        if (!$name) {
            throw new \InvalidArgumentException("Connection does not contain a 'path' or 'dbname' parameter.");
        }

        // Need to get rid of _every_ occurrence of dbname from connection configuration
        unset($params['dbname'], $params['path'], $params['url']);

        $tmpConnection = DriverManager::getConnection($params);
        $shouldNotCreateDatabase = $ifNotExists && in_array($name, $tmpConnection->getSchemaManager()->listDatabases());

        // Only quote if we don't have a path
        if (!$hasPath) {
            $name = $tmpConnection->getDatabasePlatform()->quoteSingleIdentifier($name);
        }

        $error = false;

        try {
            if ($shouldNotCreateDatabase) {
                $output->writeln(sprintf('<info>Database <comment>%s</comment> already exists.</info>', $name));
            } else {
                $tmpConnection->getSchemaManager()->createDatabase($name);
                $output->writeln(sprintf('<info>Created database <comment>%s</comment>', $name));
            }
        } catch (\Exception $e) {
            $output->writeln(sprintf('<error>Could not create database <comment>%s</comment></error>', $name));
            $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));
            $error = true;
        }

        $tmpConnection->close();

        return $error ? 1 : 0;
    }
}
