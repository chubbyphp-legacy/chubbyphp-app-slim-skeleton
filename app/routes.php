<?php

use SlimSkeleton\Controller\UserController;

/* @var \Slim\App $app */

$app->group('/users', function () use ($app) {
    $app->get('', UserController::class. ':getList');
});
