<?php

namespace SlimSkeleton\Controller;

use Chubbyphp\Security\Authentication\Exception\AbstractLoginException;
use Chubbyphp\Security\Authentication\FormAuthentication;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Chubbyphp\Session\FlashMessage;
use Chubbyphp\Session\SessionInterface;
use SlimSkeleton\Service\RedirectForPath;

final class AuthController
{
    /**
     * @var FormAuthentication
     */
    private $auth;

    /**
     * @var RedirectForPath
     */
    private $redirectForPath;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * AuthController constructor.
     * @param FormAuthentication $auth
     * @param RedirectForPath $redirectForPath
     * @param SessionInterface $session
     */
    public function __construct(FormAuthentication $auth, RedirectForPath $redirectForPath, SessionInterface $session)
    {
        $this->auth = $auth;
        $this->redirectForPath = $redirectForPath;
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

        return $this->redirectForPath->get($response, 302, 'home', ['locale' => $request->getAttribute('locale')]);
    }
}
