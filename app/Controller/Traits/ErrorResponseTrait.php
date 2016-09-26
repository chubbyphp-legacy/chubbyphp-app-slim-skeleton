<?php

namespace SlimSkeleton\Controller\Traits;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;

trait ErrorResponseTrait
{
    /**
     * @var Twig
     */
    private $twig;

    /**
     * @param Request  $request
     * @param Response $response
     * @param int      $statusCode
     * @param string   $messageTitle
     * @param string   $messageText
     *
     * @return Response
     */
    private function getErrorResponse(
        Request $request,
        Response $response,
        int $statusCode,
        string $messageTitle,
        string $messageText
    ): Response {
        return $this->twig->render($response, '@SlimSkeleton/error.html.twig',
            $this->getVariablesForTwig($request, [
                'messageTitle' => $messageTitle,
                'messageText' => $messageText,
            ])
        )->withStatus($statusCode);
    }
}
