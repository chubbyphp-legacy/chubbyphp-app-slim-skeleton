<?php

namespace SlimSkeleton\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class SchemaDumpUpdateCommand extends AbstractSchemaCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('slim-skeleton:database:schema:dump-update')
            ->setDescription(sprintf('Dump the update the database schema based on schema at "%s"', $this->schemaPath))
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

        $output->writeln('<info>Dump statements</info>');
        foreach ($statements as $statement) {
            $output->writeln($statement);
        }
    }
}
