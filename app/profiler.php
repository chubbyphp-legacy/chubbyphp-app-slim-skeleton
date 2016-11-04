<?php

use bitExpert\Http\Middleware\Psr7\Prophiler\ProphilerMiddleware;
use Doctrine\DBAL\Configuration;
use Fabfuel\Prophiler\Adapter\Doctrine\SQLLogger;
use Fabfuel\Prophiler\Adapter\Psr\Log\Logger;
use Fabfuel\Prophiler\Aggregator\Database\QueryAggregator;
use Fabfuel\Prophiler\Profiler;
use Fabfuel\Prophiler\Toolbar;
use Psr\Log\LoggerInterface;
use Slim\Container;

/* @var Container $container */
$container['dbs.config'] = function ($container) use ($container) {
    $container['dbs.options.initializer']();

    $configs = new Container();
    foreach ($container['dbs.options'] as $name => $options) {
        $config = new Configuration();
        $config->setSQLLogger(new SQLLogger($container[Profiler::class]));

        $configs[$name] = $config;
    }

    return $configs;
};

$container->extend('logger', function (LoggerInterface $logger) use ($container) {
    return new class([$logger, new Logger($container[Profiler::class])]) extends \Psr\Log\AbstractLogger {
        /**
         * @var LoggerInterface[]
         */
        private $loggers;

        /**
         * @param LoggerInterface[] $loggers
         */
        public function __construct(array $loggers)
        {
            $this->loggers = $loggers;
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
    };
});

$container[ProphilerMiddleware::class] = function () use ($container) {
    return new ProphilerMiddleware($container[Toolbar::class]);
};

$container[Profiler::class] = function () use ($container) {
    $profiler = new Profiler();

    $profiler->addAggregator(new QueryAggregator());

    return $profiler;
};

$container[Toolbar::class] = function () use ($container) {
    return new Toolbar($container[Profiler::class]);
};
