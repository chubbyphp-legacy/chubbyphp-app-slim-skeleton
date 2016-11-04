<?php

use bitExpert\Http\Middleware\Psr7\Prophiler\ProphilerMiddleware;
use Slim\App;
use Slim\Container;
use SlimSkeleton\Middleware\LazyMiddleware;
use SlimSkeleton\Middleware\LocaleMiddleware;

/* @var App $app */
/* @var Container $container */

$app->add(new LazyMiddleware($container, 'csrf.middleware'));
$app->add(new LazyMiddleware($container, 'session.middleware'));
$app->add(new LazyMiddleware($container, LocaleMiddleware::class));
$app->add(new LazyMiddleware($container, 'errorHandler.middleware'));

if ($container['debug']) {
    $app->add(new LazyMiddleware($container, ProphilerMiddleware::class));
}
