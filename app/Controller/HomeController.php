<?php

namespace SlimSkeleton\Controller;

use Chubbyphp\Security\Authentication\AuthenticationInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;
use SlimSkeleton\Controller\Traits\TwigDataTrait;
use Chubbyphp\Session\SessionInterface;

final class HomeController
{
    use TwigDataTrait;

    /**
     * @var Twig
     */
    private $twig;

    /**
     * @param AuthenticationInterface $auth
     * @param SessionInterface        $session
     * @param Twig                    $twig
     */
    public function __construct(AuthenticationInterface $auth, SessionInterface $session, Twig $twig)
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
        return $this->twig->render($response, '@SlimSkeleton/home.html.twig', $this->getTwigData($request));
    }
}
