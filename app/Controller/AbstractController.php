<?php

namespace SlimSkeleton\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Router;
use Slim\Views\Twig;
use SlimSkeleton\Auth\AuthInterface;
use SlimSkeleton\Session\SessionInterface;

abstract class AbstractController
{
    /**
     * @var AuthInterface
     */
    protected $auth;

    /**
     * @var Router
     */
    protected $router;

    /**
     * @var SessionInterface
     */
    protected $session;

    /**
     * @var Twig
     */
    protected $twig;

    /**
     * @param AuthInterface    $auth
     * @param Router           $router
     * @param SessionInterface $session
     * @param Twig             $twig
     */
    public function __construct(AuthInterface $auth, Router $router, SessionInterface $session, Twig $twig)
    {
        $this->auth = $auth;
        $this->router = $router;
        $this->session = $session;
        $this->twig = $twig;
    }

    /**
     * @param Request  $request
     * @param Response $response
     * @param int      $statusCode
     * @param string   $messageTitle
     * @param string   $messageText
     *
     * @return Response
     */
    protected function getErrorResponse(
        Request $request,
        Response $response,
        int $statusCode,
        string $messageTitle,
        string $messageText
    ): Response {
        return $this->twig->render($response, '@SlimSkeleton/error.html.twig', [
            'authenticatedUser' => prepareForView($this->auth->getAuthenticatedUser($request)),
            'messageTitle' => $messageTitle,
            'messageText' => $messageText,
        ])->withStatus($statusCode);
    }

    /**
     * @param Response $response
     * @param int      $statusCode
     * @param string   $path
     * @param array    $arguments
     *
     * @return Response
     */
    protected function getRedirectResponse(
        Response $response,
        int $statusCode,
        string $path,
        array $arguments = []
    ): Response {
        return $response->withStatus($statusCode)->withHeader('Location', $this->router->pathFor($path, $arguments));
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    protected function getGenericTwigVariables(Request $request)
    {
        return [
            'authenticatedUser' => prepareForView($this->auth->getAuthenticatedUser($request)),
            'flashMessage' => $this->session->getFlashMessage($request),
        ];
    }
}
