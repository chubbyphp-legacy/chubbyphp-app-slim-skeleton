<?php

use PSR7Session\Http\SessionMiddleware;
use Slim\App;
use Slim\Container;

/* @var App $app */
/* @var Container container */

// howto use the session `$session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);`
$app->add(SessionMiddleware::fromSymmetricKeyDefaults(
    $container['session.symmetricKey'],
    $container['session.expirationTime']
));
