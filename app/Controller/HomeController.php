<?php

namespace SlimSkeleton\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;
use SlimSkeleton\Auth\AuthInterface;
use SlimSkeleton\Controller\Traits\TwigVariablesTrait;
use SlimSkeleton\Session\SessionInterface;

class HomeController
{
    use TwigVariablesTrait;

    /**
     * @var Twig
     */
    private $twig;

    /**
     * @param AuthInterface    $auth
     * @param SessionInterface $session
     * @param Twig             $twig
     */
    public function __construct(AuthInterface $auth, SessionInterface $session, Twig $twig)
    {
        $this->auth = $auth;
        $this->session = $session;
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
        return $this->twig->render($response, '@SlimSkeleton/home.html.twig', $this->getVariablesForTwig($request));
    }
}
