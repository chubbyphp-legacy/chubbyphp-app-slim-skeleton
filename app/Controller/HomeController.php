<?php

namespace SlimSkeleton\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;
use SlimSkeleton\Auth\AuthInterface;

class HomeController
{
    /**
     * @var AuthInterface
     */
    private $auth;

    /**
     * @var Twig
     */
    private $twig;

    /**
     * @param AuthInterface $auth
     * @param Twig          $twig
     */
    public function __construct(AuthInterface $auth, Twig $twig)
    {
        $this->auth = $auth;
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
        return $this->twig->render($response, '@SlimSkeleton/home.html.twig', [
            'authenticatedUser' => prepareForView($this->auth->getAuthenticatedUser($request)),
        ]);
    }
}
