<?php

use Slim\App;
use Slim\Container;
use SlimSkeleton\Middleware\LocaleMiddleware;

/* @var App $app */
/* @var Container $container */

$app->add($container['csrf.middleware']);
$app->add($container['session.middleware']);
$app->add($container[LocaleMiddleware::class]);
