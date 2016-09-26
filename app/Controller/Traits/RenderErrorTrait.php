<?php

namespace SlimSkeleton\Controller\Traits;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;

trait RenderErrorTrait
{
    /**
     * @var Twig
     */
    private $twig;

    /**
     * @param Request  $request
     * @param Response $response
     * @param int      $code
     * @param string   $title
     * @param string   $body
     *
     * @return Response
     */
    private function renderError(Request $request, Response $response, int $code, string $title, string $body): Response
    {
        return $this->twig->render($response, '@SlimSkeleton/error.html.twig',
            $this->getTwigData($request, [
                'messageTitle' => $title,
                'messageText' => $body,
            ])
        )->withStatus($code);
    }
}
