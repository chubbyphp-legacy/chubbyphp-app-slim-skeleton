<?php

use Slim\App;
use Slim\Collection;
use Slim\Container;

$container = new Container();
$container['appDir'] = __DIR__;
$container['cacheDir'] = $container['appDir'].'/../var/cache';
$container['configDir'] = $container['appDir'].'/../config';

require_once $container['appDir'].'/functions.php';

$config = array_replace_recursive(
    require $container['configDir'].'/config.php',
    require $container['configDir'].'/config_'.$env.'.php'
);

// slim settings
$container->extend('settings', function (Collection $settings) use ($config) {
    $settings->replace($config['settings']);

    return $settings;
});

require_once $container['appDir'].'/services.php';

// project settings
foreach ($config['projectSettings'] as $key => $value) {
    $container[$key] = $value;
}

$app = new App($container);

require_once $container['appDir'].'/middlewares.php';
require_once $container['appDir'].'/routes.php';

return $app;
