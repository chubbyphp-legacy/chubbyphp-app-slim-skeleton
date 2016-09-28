<?php

namespace SlimSkeleton\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Router;
use SlimSkeleton\Security\AuthInterface;
use SlimSkeleton\Security\Exception\AbstractLoginException;
use SlimSkeleton\Controller\Traits\RedirectForPathTrait;
use SlimSkeleton\Session\FlashMessage;
use SlimSkeleton\Session\SessionInterface;

final class AuthController
{
    use RedirectForPathTrait;

    /**
     * @var AuthInterface
     */
    private $auth;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * AuthController constructor.
     *
     * @param AuthInterface    $auth
     * @param SessionInterface $session
     */
    public function __construct(AuthInterface $auth, Router $router, SessionInterface $session)
    {
        $this->auth = $auth;
        $this->router = $router;
        $this->session = $session;
    }

    /**
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     */
    public function login(Request $request, Response $response): Response
    {
        try {
            $this->auth->login($request);
        } catch (AbstractLoginException $e) {
            $flashMessage = new FlashMessage(FlashMessage::TYPE_DANGER, 'login.flash.invalidcredentials');
            $this->session->addFlash($request, $flashMessage);
        }

        return $response->withStatus(302)->withHeader('Location', $request->getHeader('Referer')[0]);
    }

    /**
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     */
    public function logout(Request $request, Response $response): Response
    {
        $this->auth->logout($request);

        return $this->getRedirectForPath($response, 302, 'home', ['locale' => $request->getAttribute('locale')]);
    }
}
