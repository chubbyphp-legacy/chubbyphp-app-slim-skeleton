<?php

namespace SlimSkeleton\Controller\Traits;

use Psr\Http\Message\ResponseInterface as Response;
use Slim\Router;

trait RedirectForPathTrait
{
    /**
     * @var Router
     */
    private $router;

    /**
     * @param Response $response
     * @param int      $statusCode
     * @param string   $path
     * @param array    $arguments
     *
     * @return Response
     */
    private function getRedirectForPath(
        Response $response,
        int $statusCode,
        string $path,
        array $arguments = []
    ): Response {
        return $response
            ->withStatus($statusCode)
            ->withHeader('Location', $this->router->pathFor($path, $arguments))
        ;
    }
}
