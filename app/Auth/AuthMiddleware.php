<?php

namespace SlimSkeleton\Auth;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

final class AuthMiddleware
{
    /**
     * @var AuthInterface
     */
    private $auth;

    /**
     * @param AuthInterface $auth
     */
    public function __construct(AuthInterface $auth)
    {
        $this->auth = $auth;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param callable $next
     * @return Response
     */
    public function __invoke(Request $request, Response $response, callable $next)
    {
        if (!$this->auth->isAuthenticated($request)) {
            throw new \Exception('Permission denied');
        }

        $next($request, $response);

        return $response;
    }
}
