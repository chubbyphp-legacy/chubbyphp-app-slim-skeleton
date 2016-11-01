<?php

namespace SlimSkeleton\Command;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;

abstract class AbstractSchemaCommand
{
    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var
     */
    protected $schemaPath;

    /**
     * @param Connection $connection
     * @param string     $schemaPath
     */
    public function __construct(Connection $connection, string $schemaPath)
    {
        $this->connection = $connection;
        $this->schemaPath = $schemaPath;
    }

    /**
     * @return array
     */
    protected function getStatements(): array
    {
        $connection = $this->connection;

        $schemaManager = $connection->getSchemaManager();
        $fromSchema = $schemaManager->createSchema();

        /** @var Schema $schema */
        $schema = require $this->schemaPath;

        return $fromSchema->getMigrateToSql($schema, $connection->getDatabasePlatform());
    }
}
