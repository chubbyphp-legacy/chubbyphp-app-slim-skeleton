<?php

declare(strict_types=1);

namespace SlimSkeleton\Provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

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
            $twig = new \Twig_Environment($container['twig.loader'], [
                'cache' => !$container['debug'] ? $container['cacheDir'].'/twig' : null,
                'debug' => $container['debug'],
            ]);

            foreach ($container['twig.extensions'] as $extension) {
                $twig->addExtension($extension);
            }

            foreach ($container['twig.globals'] as $name => $value) {
                $twig->addGlobal($name, $value);
            }

            return $twig;
        };

        $container['twig.loader'] = function () use ($container) {
            $loader = new \Twig_Loader_Filesystem();
            foreach ($container['twig.namespaces'] as $namespace => $path) {
                $loader->addPath($path, $namespace);
            }

            return $loader;
        };
    }
}
