<?php

namespace SlimSkeleton\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;
use SlimSkeleton\Service\TemplateData;

final class HomeController
{
    /**
     * @var TemplateData
     */
    private $templateData;

    /**
     * @var Twig
     */
    private $twig;

    /**
     * @param TemplateData $templateData
     * @param Twig         $twig
     */
    public function __construct(TemplateData $templateData, Twig $twig)
    {
        $this->templateData = $templateData;
        $this->twig = $twig;
    }

    /**
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     */
    public function home(Request $request, Response $response)
    {
        return $this->twig->render($response, '@SlimSkeleton/home.html.twig', $this->templateData->aggregate($request));
    }
}
