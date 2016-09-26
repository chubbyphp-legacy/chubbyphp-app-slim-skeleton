<?php

use PSR7Session\Http\SessionMiddleware;
use Slim\App;
use Slim\Container;
use SlimSkeleton\Middleware\LocaleMiddleware;

/* @var App $app */
/* @var Container container */

$app->add($container[LocaleMiddleware::class]);
$app->add($container[SessionMiddleware::class]);
