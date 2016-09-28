<?php

use PSR7Session\Http\SessionMiddleware;
use Slim\App;
use Slim\Container;
use SlimSkeleton\Auth\CsrfTokenMiddleware;
use SlimSkeleton\Middleware\LocaleMiddleware;

/* @var App $app */
/* @var Container container */

$app->add($container[CsrfTokenMiddleware::class]);
$app->add($container[SessionMiddleware::class]);
$app->add($container[LocaleMiddleware::class]);
