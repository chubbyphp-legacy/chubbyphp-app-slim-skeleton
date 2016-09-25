<?php

namespace SlimSkeleton\Auth;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;

final class AuthMiddleware
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
     * @param callable $next
     *
     * @return Response
     */
    public function __invoke(Request $request, Response $response, callable $next)
    {
        if (!$this->auth->isAuthenticated($request)) {
            return $this->twig->render($response, '@SlimSkeleton/error.html.twig', [
                'authenticatedUser' => null,
                'messageTitle' => 'Permission denied',
                'messageText' => 'For this route is a login needed!',
            ])->withStatus(403);
        }

        $response = $next($request, $response);

        return $response;
    }
}
