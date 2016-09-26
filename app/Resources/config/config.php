<?php

return [
    'settings' => [
        'displayErrorDetails' => false,
    ],
    'projectSettings' => [
        'db.options' => [
            'driver'    => 'pdo_mysql',
            'host'      => 'localhost',
            'dbname'    => 'slim_skeleton',
            'user'      => 'root',
            'password'  => 'root',
            'charset'   => 'utf8',
        ],
        'debug' => false,
        'session.symmetricKey' => '6t332+EAscTgRQstgHjUOYvTeTbhk7CaW9AptDT9Fhw=', // https://github.com/AndrewCarterUK/CryptoKey
        'session.expirationTime' => 1200
    ],
];
