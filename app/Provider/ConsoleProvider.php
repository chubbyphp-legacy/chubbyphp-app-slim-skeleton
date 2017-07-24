<?php

declare(strict_types=1);

namespace SlimSkeleton\Provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

final class ConsoleProvider implements ServiceProviderInterface
{
    /**
     * @param Container $container
     */
    public function register(Container $container)
    {
        $container['console.name'] = 'slim-skeleton';
        $container['console.version'] = '1.0';

        $container['console.helpers'] = function () {
            return [];
        };

        $container['console.commands'] = function () {
            return [];
        };
    }
}
