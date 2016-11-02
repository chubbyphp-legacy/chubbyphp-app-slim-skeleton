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
     *
     * @return int|null
     */
    public function __invoke(InputInterface $input, OutputInterface $output)
    {
        $dump = true === $input->getOption('dump');
        $force = true === $input->getOption('force');

        if ([] === $statements = $this->getStatements()) {
            $output->writeln('<info>No schema changes required</info>');

            return 0;
        }

        if (!$dump && !$force) {
            $output->writeln('<comment>ATTENTION</comment>: Do not execute in production.');
            $output->writeln('    Use the incremental update to detect changes during development and use');
            $output->writeln('    the SQL provided to manually update your database in production.');
            $output->writeln('');
            $output->writeln(sprintf('Would execute <info>"%s"</info> queries.', count($statements)));
            $output->writeln('Please run the operation by passing one - or both - of the following options:');
            $output->writeln('    <info>--force</info> to execute the command');
            $output->writeln('    <info>--dump</info> to dump the SQL statements to the screen');

            return 1;
        }

        $this->update($output, $statements, $dump, $force);

        return 0;
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

    /**
     * @param OutputInterface $output
     * @param array           $statements
     * @param bool            $dump
     * @param bool            $force
     */
    private function update(OutputInterface $output, array $statements, bool $dump, bool $force)
    {
        if ($dump) {
            $output->writeln('<info>Begin transaction</info>');
        }

        if ($force) {
            $this->connection->beginTransaction();
        }

        foreach ($statements as $statement) {
            if ($dump) {
                $output->writeln($statement);
            }

            if ($force) {
                $this->connection->exec($statement);
            }
        }

        if ($dump) {
            $output->writeln('<info>Commit</info>');
        }

        if ($force) {
            $this->connection->commit();
        }
    }
}
