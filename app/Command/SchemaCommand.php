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
    private $schemaDir;

    /**
     * @param string $schemaDir
     */
    public function __construct(string $schemaDir)
    {
        $this->schemaDir = $schemaDir;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('slim-skeleton:database:schema:update')
            ->setDescription(sprintf('Update the database schema based on configuration at "%s"', $this->schemaDir))
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
        $schema = new Schema();

        foreach (glob($this->schemaDir.'/*.php') as $tableSchemaFile) {
            require $tableSchemaFile;
        }

        $statements = $fromSchema->getMigrateToSql($schema, $connection->getDatabasePlatform());

        $connection->beginTransaction();
        foreach ($statements as $statement) {
            $connection->exec($statement);
        }
        $connection->commit();
    }
}
