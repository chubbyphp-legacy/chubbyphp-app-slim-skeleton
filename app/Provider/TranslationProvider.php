<?php

namespace SlimSkeleton\Provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use SlimSkeleton\Translation\Translator;

final class TranslationProvider implements ServiceProviderInterface
{
    /**
     * @param Container $container
     */
    public function register(Container $container)
    {
        $container['translator.providers'] = function () use ($container) {
            return [];
        };

        $container['translator'] = function () use ($container) {
            return new Translator($container['translator.providers']);
        };
    }
}
