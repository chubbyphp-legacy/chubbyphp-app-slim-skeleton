<?php

namespace SlimSkeleton\Service;

use Psr\Http\Message\ResponseInterface as Response;

final class TwigRender
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * TwigRender constructor.
     *
     * @param \Twig_Environment $twig
     */
    public function __construct(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * @param Response $response
     * @param $name
     * @param array $context
     *
     * @return Response
     */
    public function render(Response $response, $name, array $context = []): Response
    {
        $response->getBody()->write($this->twig->render($name, $context));

        return $response;
    }
}
