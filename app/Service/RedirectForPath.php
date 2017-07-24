<?php

declare(strict_types=1);

namespace SlimSkeleton\Service;

use Psr\Http\Message\ResponseInterface as Response;
use Slim\Router;

final class RedirectForPath
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
     * @param Response $response
     * @param int      $status
     * @param string   $path
     * @param array    $arguments
     *
     * @return Response
     */
    public function get(Response $response, int $status, string $path, array $arguments = []): Response
    {
        return $response->withStatus($status)->withHeader('Location', $this->router->pathFor($path, $arguments));
    }
}
