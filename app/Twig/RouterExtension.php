<?php

declare(strict_types=1);

namespace SlimSkeleton\Twig;

use Slim\Router;

final class RouterExtension extends \Twig_Extension
{
    /**
     * @var Router
     */
    private $router;

    /**
     * @param Router $router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('path_for', [$this, 'pathFor']),
        ];
    }

    /**
     * @param string $name
     * @param array  $data
     * @param array  $queryParams
     *
     * @return string
     */
    public function pathFor(string $name, $data = [], $queryParams = []): string
    {
        return $this->router->pathFor($name, $data, $queryParams);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'router';
    }
}
