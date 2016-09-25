<?php

use PSR7Session\Http\SessionMiddleware;
use Slim\App;
use Slim\Container;

/* @var App $app */
/* @var Container container */

$app->add($container[SessionMiddleware::class]);
