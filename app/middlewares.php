<?php

use bitExpert\Http\Middleware\Psr7\Prophiler\ProphilerMiddleware;
use Slim\App;
use Slim\Container;
use SlimSkeleton\Middleware\LocaleMiddleware;

/* @var App $app */
/* @var Container $container */

$app->add($container['csrf.middleware']);
$app->add($container['session.middleware']);
$app->add($container[LocaleMiddleware::class]);

if ($container['debug']) {
    $app->add($container[ProphilerMiddleware::class]);
}
