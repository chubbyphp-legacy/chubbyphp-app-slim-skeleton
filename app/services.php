<?php

use Slim\Container;
use SlimSkeleton\Provider\ConsoleProvider;
use SlimSkeleton\Provider\TwigProvider;

/* @var Container $container */

$container->register(new ConsoleProvider());
$container->register(new TwigProvider());

$container->extend('twig.namespaces', function (array $namespaces) use ($container) {
    $namespaces['SlimSkeleton'] = $container['appDir'].'/Resources/views';

    return $namespaces;
});
