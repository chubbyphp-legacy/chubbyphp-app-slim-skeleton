<?php

use Slim\App;
use Slim\Container;
use SlimSkeleton\Controller\HomeController;
use SlimSkeleton\Controller\UserController;
use SlimSkeleton\Middleware\AuthMiddleware;

/* @var App $app */
/* @var Container $app */

$app->get('/', HomeController::class. ':home')->setName('home');
$app->get('/users', UserController::class. ':listAll')->setName('user_list')->add($container[AuthMiddleware::class]);
$app->get('/users/{id}', UserController::class. ':view')->setName('user_view')->add($container[AuthMiddleware::class]);
