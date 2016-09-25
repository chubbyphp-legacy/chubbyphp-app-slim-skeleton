<?php

namespace SlimSkeleton\Provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Slim\Views\Twig;

final class TwigProvider implements ServiceProviderInterface
{
    /**
     * @param Container $container
     */
    public function register(Container $container)
    {
        $container['twig.namespaces'] = function () {
            return [];
        };

        $container['twig.extensions'] = function () use ($container) {
            return [];
        };

        $container['twig.globals'] = function () use ($container) {
            return [];
        };

        $container['twig'] = function () use ($container) {
            $twig = new Twig(
                $container['twig.namespaces'],
                [
                    'cache' => $container['cacheDir'].'/twig',
                    'debug' => $container['debug'],
                ]
            );

            foreach ($container['twig.extensions'] as $extension) {
                $twig->addExtension($extension);
            }

            foreach ($container['twig.globals'] as $name => $value) {
                $twig->getEnvironment()->addGlobal($name, $value);
            }

            return $twig;
        };
    }
}
