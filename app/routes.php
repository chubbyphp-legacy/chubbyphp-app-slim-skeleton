<?php

use SlimSkeleton\Controller\HomeController;
use SlimSkeleton\Controller\UserController;

/* @var \Slim\App $app */

$app->get('/', HomeController::class. ':home')->setName('home');
$app->get('/users', UserController::class. ':listAll')->setName('user_list');
$app->get('/users/{id}', UserController::class. ':view')->setName('user_view');
