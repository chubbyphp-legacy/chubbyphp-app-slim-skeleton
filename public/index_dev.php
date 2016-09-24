<?php

function checkAllowedIp($remoteAddress)
{
    if(in_array($remoteAddress, array('127.0.0.1', 'fe80::1', '::1'), true)) {
        return true;
    }
    $matches = array();
    // http://en.wikipedia.org/wiki/Private_network
    if(preg_match('/([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})/', $remoteAddress, $matches) === 1) {
        for($i=1;$i<5;$i++) {
            $matches[$i] = (int) $matches[$i];
        }
        // localhost
        if($matches[1] === 127) {
            return true;
        }
        if($matches[1] === 10) {
            return true;
        }
        if($matches[1] === 172 && $matches[2] >= 16 && $matches[2] <= 31) {
            return true;
        }
        if($matches[1] === 192 && $matches[2] === 168) {
            return true;
        }
    }
}

if (isset($_SERVER['HTTP_CLIENT_IP'])
    || isset($_SERVER['HTTP_X_FORWARDED_FOR'])
    || !(checkAllowedIp($_SERVER['REMOTE_ADDR']) || php_sapi_name() === 'cli-server')
) {
    header('HTTP/1.0 403 Forbidden');
    exit('You are not allowed to access this file. Check '.basename(__FILE__).' for more information.');
}

$loader = require_once __DIR__.'/../app/autoload.php';

$env = 'dev';

/** @var \Slim\App $app */
$app = require_once __DIR__ . '/../app/app.php';

$app->run();
