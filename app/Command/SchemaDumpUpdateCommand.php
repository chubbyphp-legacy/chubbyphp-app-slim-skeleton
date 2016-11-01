<?php

namespace SlimSkeleton\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class SchemaDumpUpdateCommand extends AbstractSchemaCommand
{
    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    public function __invoke(InputInterface $input, OutputInterface $output)
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
