<?php

namespace SlimSkeleton\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Router;
use SlimSkeleton\Auth\AuthInterface;
use SlimSkeleton\Auth\Exception\AbstractAuthException;

class AuthController
{
    /**
     * @var AuthInterface
     */
    private $auth;

    /**
     * @var Router
     */
    private $router;

    /**
     * @param AuthInterface $auth
     * @param Router $router
     */
    public function __construct(AuthInterface $auth, Router $router)
    {
        $this->auth = $auth;
        $this->router = $router;
    }

    /**
     * @param Request $request
     * @param Response $response
     */
    public function authenticate(Request $request, Response $response)
    {
        try {
            $this->auth->authenticate($request);
            return $response->withHeader('Location', $this->router->pathFor('home'));
        } catch (AbstractAuthException $e) {
            throw $e;
        }
    }
}
