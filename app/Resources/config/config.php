<?php

return [
    'settings' => [
        'httpVersion' => '1.1',
        'responseChunkSize' => 4096,
        'outputBuffering' => 'append',
        'determineRouteBeforeAppMiddleware' => true,
        'displayErrorDetails' => false,
        'addContentLengthHeader' => true,
        'routerCacheFile' => $container['cacheDir'].'/routes.php',
    ],
    'projectSettings' => [
        'db.options' => [
            'driver' => 'pdo_mysql',
            'host' => 'localhost',
            'dbname' => 'slim_skeleton',
            'user' => 'root',
            'password' => 'root',
            'charset' => 'utf8',
        ],
        'debug' => false,
        'localeFallback' => 'en',
        'locales' => ['de', 'en'],
        'session.expirationTime' => 1200,
        'session.privateRsaKey' => '6t332+EAscTgRQstgHjUOYvTeTbhk7CaW9AptDT9Fhw=', // https://github.com/AndrewCarterUK/CryptoKey
        'session.publicRsaKey' => '6t332+EAscTgRQstgHjUOYvTeTbhk7CaW9AptDT9Fhw=',
        'session.setCookieSecureOnly' => false,
    ],
];
