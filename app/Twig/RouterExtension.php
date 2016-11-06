<?php

namespace SlimSkeleton\Twig;

use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Route;
use Slim\Router;

final class RouterExtension extends \Twig_Extension
{
    /**
     * @var Router
     */
    private $router;

    /**
     * @var array
     */
    private $routeHirarchy;

    /**
     * @param Router $router
     */
    public function __construct(Router $router, array $routeHirarchy = [])
    {
        $this->router = $router;
        $this->routeHirarchy = $routeHirarchy;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('path_for', [$this, 'pathFor']),
            new \Twig_SimpleFunction('trail_for', [$this, 'getTrailFor']),
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

    public function getTrailFor(Request $request)
    {
        /** @var Route $route */
        $route = $request->getAttribute('route');

        $route->getName();

        return $this->generateTrail($route->getName());
    }

    /**
     * @param string $activeName
     *
     * @return array
     */
    private function generateTrail(string $activeName): array
    {
        $inTrail = [];
        foreach ($this->routeHirarchy as $routeName => $subRouteNames) {
            if ($routeName === $activeName) {
                $inTrail[] = $routeName;
            }
            foreach ($subRouteNames as $subRouteName) {
                if ($subRouteName === $activeName) {
                    $inTrail[] = $subRouteName;
                    $inTrail = array_merge($inTrail, $this->generateTrail($routeName));
                }
            }
        }

        return array_unique($inTrail);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'router';
    }
}
