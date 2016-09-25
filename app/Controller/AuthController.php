<?php

namespace SlimSkeleton\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use SlimSkeleton\Auth\Exception\AbstractLoginException;

class AuthController extends AbstractController
{
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
            $this->session->set($request, 'f', ['t' => 'danger', 'm' => 'Invalid credentials']);
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

        return $this->getRedirectResponse($response, 302, 'home');
    }
}
