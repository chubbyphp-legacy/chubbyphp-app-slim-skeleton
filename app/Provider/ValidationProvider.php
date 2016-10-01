<?php

namespace SlimSkeleton\Provider;

use Chubbyphp\Validation\Validator;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

final class ValidationProvider implements ServiceProviderInterface
{
    /**
     * @param Container $container
     */
    public function register(Container $container)
    {
        $container['validator.repositories'] = function () {
            return [];
        };

        $container['validator'] = function () use ($container) {
            return new Validator($container['validator.repositories']);
        };
    }
}
