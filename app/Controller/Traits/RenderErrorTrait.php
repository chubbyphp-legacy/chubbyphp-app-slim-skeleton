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
     *
     * @return Response
     */
    private function renderError(Request $request, Response $response, int $code): Response
    {
        return $this->twig->render($response, '@SlimSkeleton/error.html.twig',
            $this->getTwigData($request, ['code' => $code])
        )->withStatus($code);
    }
}
