<?php

namespace SlimSkeleton\Command;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class SchemaUpdateCommand
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
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    public function __invoke(InputInterface $input, OutputInterface $output)
    {
        $dumpSql = true === $input->getOption('dump-sql');
        $force = true === $input->getOption('force');

        if ([] === $statements = $this->getStatements()) {
            $output->writeln('<info>No schema changes required</info>');

            return 0;
        }

        if (!$dumpSql && !$force) {
            $output->writeln('<comment>ATTENTION</comment>: This operation should not be executed in a production environment.');
            $output->writeln('           Use the incremental update to detect changes during development and use');
            $output->writeln('           the SQL DDL provided to manually update your database in production.');
            $output->writeln('');
            $output->writeln(sprintf('The Schema-Tool would execute <info>"%s"</info> queries to update the database.', count($statements)));
            $output->writeln('Please run the operation by passing one - or both - of the following options:');
            $output->writeln('    <info>--force</info> to execute the command');
            $output->writeln('    <info>--dump-sql</info> to dump the SQL statements to the screen');

            return 1;
        }

        if ($force) {
            $this->forceUpdate($output, $statements);

            return 0;
        }

        $this->dumpUpdate($output, $statements);

        return 0;
    }

    /**
     * @param OutputInterface $output
     * @param array           $statements
     */
    private function forceUpdate(OutputInterface $output, array $statements)
    {
        $output->writeln('<info>Begin transaction</info>');
        $this->connection->beginTransaction();

        foreach ($statements as $statement) {
            $output->writeln($statement);
            $this->connection->exec($statement);
        }

        $output->writeln('<info>Commit</info>');
        $this->connection->commit();
    }

    /**
     * @param OutputInterface $output
     * @param array           $statements
     */
    private function dumpUpdate(OutputInterface $output, array $statements)
    {
        foreach ($statements as $statement) {
            $output->writeln($statement);
        }
    }

    /**
     * @return array
     */
    private function getStatements(): array
    {
        $connection = $this->connection;

        $schemaManager = $connection->getSchemaManager();
        $fromSchema = $schemaManager->createSchema();

        /** @var Schema $schema */
        $schema = require $this->schemaPath;

        return $fromSchema->getMigrateToSql($schema, $connection->getDatabasePlatform());
    }
}
