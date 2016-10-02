<?php

use Slim\App;
use Slim\Container;
use SlimSkeleton\Security\CsrfTokenMiddleware;
use SlimSkeleton\Middleware\LocaleMiddleware;

/* @var App $app */
/* @var Container container */

$app->add($container[CsrfTokenMiddleware::class]);
$app->add($container['session.middleware']);
$app->add($container[LocaleMiddleware::class]);
