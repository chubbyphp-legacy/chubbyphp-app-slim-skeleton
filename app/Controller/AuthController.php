<?php

namespace SlimSkeleton\Controller;

use Chubbyphp\Security\Authentication\Exception\AbstractLoginException;
use Chubbyphp\Security\Authentication\FormAuthentication;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Router;
use SlimSkeleton\Controller\Traits\RedirectForPathTrait;
use Chubbyphp\Session\FlashMessage;
use Chubbyphp\Session\SessionInterface;

final class AuthController
{
    use RedirectForPathTrait;

    /**
     * @var FormAuthentication
     */
    private $auth;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * AuthController constructor.
     *
     * @param FormAuthentication $auth
     * @param SessionInterface   $session
     */
    public function __construct(FormAuthentication $auth, Router $router, SessionInterface $session)
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
