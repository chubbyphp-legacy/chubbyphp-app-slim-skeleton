<?php

$loader = require_once __DIR__.'/../app/autoload.php';

$env = 'prod';

/** @var \Slim\App $app */
$app = require_once __DIR__ . '/../app/app.php';

$app->run();
