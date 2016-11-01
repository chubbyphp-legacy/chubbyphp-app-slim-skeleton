<?php

namespace SlimSkeleton\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LazyCommand extends Command
{
    /**
     * @var callable
     */
    private $command;

    /**
     * @param string   $name
     * @param string   $description
     * @param array    $definition
     * @param callable $command
     */
    public function __construct(
        string $name,
        string $description,
        array $definition,
        callable $command
    ) {
        $this->command = $command;

        parent::__construct($name);

        $this->setDescription($description);
        $this->setDefinition($definition);
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $command = $this->command;

        return $command($input, $output);
    }
}
