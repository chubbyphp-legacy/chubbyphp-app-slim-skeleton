<?php

namespace SlimSkeleton\Security;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;
use SlimSkeleton\Controller\Traits\RenderErrorTrait;
use SlimSkeleton\Controller\Traits\TwigDataTrait;
use SlimSkeleton\Session\SessionInterface;

final class AuthMiddleware
{
    use RenderErrorTrait;
    use TwigDataTrait;

    /**
     * @var AuthInterface
     */
    private $auth;

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
     * @param callable $next
     *
     * @return Response
     */
    public function __invoke(Request $request, Response $response, callable $next)
    {
        if (!$this->auth->isAuthenticated($request)) {
            return $this->renderError($request, $response, 401);
        }

        $response = $next($request, $response);

        return $response;
    }
}
