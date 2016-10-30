<?php

namespace SlimSkeleton\Command;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SchemaCommand extends Command
{
    /**
     * @var
     */
    private $schemaPath;

    /**
     * @param string $schemaPath
     */
    public function __construct(string $schemaPath)
    {
        $this->schemaPath = $schemaPath;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('slim-skeleton:database:schema:update')
            ->setDescription(sprintf('Update the database schema based on schema at "%s"', $this->schemaPath))
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var Connection $connection */
        $connection = $this->getHelper('db')->getConnection();

        $schemaManager = $connection->getSchemaManager();
        $fromSchema = $schemaManager->createSchema();

        /** @var Schema $schema */
        $schema = require $this->schemaPath;

        $statements = $fromSchema->getMigrateToSql($schema, $connection->getDatabasePlatform());

        $connection->beginTransaction();
        foreach ($statements as $statement) {
            $output->writeln(sprintf('Execute: %s', $statement));
            $connection->exec($statement);
        }
        $connection->commit();
    }
}
