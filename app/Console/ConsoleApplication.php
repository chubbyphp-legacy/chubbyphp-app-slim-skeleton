<?php

namespace SlimSkeleton\Console;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputOption;

final class ConsoleApplication extends Application
{
    /**
     * @param string              $name
     * @param string              $version
     * @param InputOption[]|array $inputOptions
     */
    public function __construct(string $name = 'UNKNOWN', string $version = 'UNKNOWN', array $inputOptions = [])
    {
        parent::__construct($name, $version);

        foreach ($inputOptions as $inputOption) {
            $this->getDefinition()->addOption($inputOption);
        }
    }
}
