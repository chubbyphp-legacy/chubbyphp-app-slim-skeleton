<?php

namespace SlimSkeleton\Middleware;

use Chubbyphp\ErrorHandler\HttpException;
use Chubbyphp\ErrorHandler\Slim\ErrorHandlerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

final class ErrorHandlerMiddleware
{
    /**
     * @var ErrorHandlerInterface
     */
    private $errorHandler;

    /**
     * @param ErrorHandlerInterface $errorHandler
     */
    public function __construct(ErrorHandlerInterface $errorHandler)
    {
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
        try {
            return $next($request, $response);
        } catch (HttpException $e) {
            $errorHandler = $this->errorHandler;

            return $errorHandler($request, $response, $e);
        }
    }
}
