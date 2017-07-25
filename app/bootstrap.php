<?php

use Slim\Collection;
use Slim\Container;

$container = new Container();

$container['cacheDir'] = __DIR__.'/../var/cache';
$container['configDir'] = __DIR__.'/../config';
$container['logDir'] = __DIR__.'/../var/log';
$container['publicDir'] = __DIR__.'/../public';
$container['translationDir'] = __DIR__.'/../translations';
$container['viewDir'] = __DIR__.'/../views';
$container['vendorDir'] = __DIR__.'/../vendor';

require_once __DIR__.'/functions.php';

$config = array_replace_recursive(
    require $container['configDir'].'/config.php',
    require $container['configDir'].'/config_'.$env.'.php'
);

// slim settings
$container->extend('settings', function (Collection $settings) use ($config) {
    $settings->replace($config['settings']);

    return $settings;
});

require_once __DIR__.'/services.php';

// project settings
foreach ($config['projectSettings'] as $key => $value) {
    $container[$key] = $value;
}

return $container;
