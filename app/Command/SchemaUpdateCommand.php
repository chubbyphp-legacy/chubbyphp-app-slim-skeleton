<?php

namespace SlimSkeleton\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class SchemaUpdateCommand extends AbstractSchemaCommand
{
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
        if ([] === $statements = $this->getStatements()) {
            $output->writeln('<info>No schema changes required</info>');

            return;
        }

        $output->writeln('<info>Begin transaction</info>');
        $this->connection->beginTransaction();

        foreach ($statements as $statement) {
            $output->writeln($statement);
            $this->connection->exec($statement);
        }

        $output->writeln('<info>Commit</info>');
        $this->connection->commit();
    }
}
