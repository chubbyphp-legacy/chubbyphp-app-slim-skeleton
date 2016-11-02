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
     * @param string      $name
     * @param array       $definition
     * @param callable    $command
     * @param string|null $description
     * @param string|null $help
     */
    public function __construct(
        string $name,
        array $definition,
        callable $command,
        string $description = null,
        string $help = null
    ) {
        parent::__construct($name);

        $this->command = $command;

        $this->setDefinition($definition);
        $this->setDescription($description);
        $this->setHelp($help);
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $command = $this->command;

        return $command($input, $output);
    }
}
