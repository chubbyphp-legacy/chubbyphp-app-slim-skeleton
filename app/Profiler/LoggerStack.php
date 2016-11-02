<?php

namespace SlimSkeleton\Profiler;

use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;

class LoggerStack extends AbstractLogger
{
    /**
     * @var LoggerInterface[]
     */
    private $loggers;

    /**
     * @param \Psr\Log\LoggerInterface[] $loggers
     */
    public function __construct(array $loggers)
    {
        foreach ($loggers as $logger) {
            $this->addLogger($logger);
        }
    }

    /**
     * @param LoggerInterface $logger
     */
    private function addLogger(LoggerInterface $logger)
    {
        $this->loggers[] = $logger;
    }

    /**
     * @param string $level
     * @param string $message
     * @param array  $context
     */
    public function log($level, $message, array $context = [])
    {
        foreach ($this->loggers as $logger) {
            $logger->log($level, $message, $context);
        }
    }
}
