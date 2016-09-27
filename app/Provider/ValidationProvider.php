<?php

namespace SlimSkeleton\Provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use SlimSkeleton\Validation\Validator;

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
