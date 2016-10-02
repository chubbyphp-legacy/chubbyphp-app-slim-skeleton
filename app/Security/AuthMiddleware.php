<?php

namespace SlimSkeleton\Security;

use Chubbyphp\ErrorHandler\ErrorHandlerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

final class AuthMiddleware
{
    /**
     * @var AuthInterface
     */
    private $auth;

    /**
     * @var ErrorHandlerInterface
     */
    private $errorHandler;

    /**
     * @param AuthInterface         $auth
     * @param ErrorHandlerInterface $errorHandler
     */
    public function __construct(AuthInterface $auth, ErrorHandlerInterface $errorHandler)
    {
        $this->auth = $auth;
        $this->errorHandler = $errorHandler;
    }

    /**
     * @param Request  $request
     * @param Response $response
     * @param callable $next
     *
     * @return Response
     */
    public function __invoke(Request $request, Response $response, callable $next)
    {
        if (!$this->auth->isAuthenticated($request)) {
            return $this->errorHandler->error($request, $response, 401);
        }

        $response = $next($request, $response);

        return $response;
    }
}
