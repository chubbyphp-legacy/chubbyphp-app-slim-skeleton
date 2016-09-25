<?php

namespace SlimSkeleton\Provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Slim\Views\Twig;
use Slim\Views\TwigExtension;

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
            $extensions = [];
            $extensions[] = new TwigExtension(
                $container['router'],
                rtrim(str_ireplace('index.php', '', $container['request']->getUri()->getBasePath()), '/')
            );
            $extensions[] = new \Twig_Extension_Debug();

            return $extensions;
        };

        $container['twig'] = function () use ($container) {
            $twig = new Twig(
                $container['twig.namespaces'],
                [
                    'cache' => $container['cacheDir'].'/twig',
                    'debug' => $container['debug']
                ]
            );

            foreach ($container['twig.extensions'] as $extension) {
                $twig->addExtension($extension);
            }

            return $twig;
        };
    }
}
