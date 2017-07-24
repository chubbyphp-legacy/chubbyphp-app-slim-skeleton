<?php

declare(strict_types=1);

namespace SlimSkeleton\Security;

use Chubbyphp\Security\Authentication\AuthenticationErrorHandlerInterface;
use SlimSkeleton\ErrorHandler\ErrorResponseHandler;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class AuthenticationErrorHandler implements AuthenticationErrorHandlerInterface
{
    /**
     * @var ErrorResponseHandler
     */
    private $errorResponseHandler;

    /**
     * @param ErrorResponseHandler $errorResponseHandler
     */
    public function __construct(ErrorResponseHandler $errorResponseHandler)
    {
        $this->errorResponseHandler = $errorResponseHandler;
    }

    /**
     * @param Request  $request
     * @param Response $response
     * @param int      $code
     *
     * @return Response
     */
    public function errorResponse(Request $request, Response $response, int $code): Response
    {
        return $this->errorResponseHandler->errorReponse($request, $response, $code);
    }
}
