<?php

namespace SlimSkeleton\Service;

use Chubbyphp\Security\Authentication\AuthenticationInterface;
use Chubbyphp\Session\SessionInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Route;

final class TemplateData
{
    /**
     * @var AuthenticationInterface
     */
    private $auth;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @param AuthenticationInterface $auth
     * @param SessionInterface        $session
     */
    public function __construct(AuthenticationInterface $auth, SessionInterface $session)
    {
        $this->auth = $auth;
        $this->session = $session;
    }

    /**
     * @param Request $request
     * @param array   $variables
     *
     * @return array
     */
    public function aggregate(Request $request, array $variables = []): array
    {
        if (null === $locale = $request->getAttribute('locale')) {
            /* @var Route $route */
            if (null === $route = $request->getAttribute('route')) {
                throw new \RuntimeException('There was no way to resolve a locale!');
            }

            $locale = $route->getArgument('locale');
        }

        return array_replace_recursive([
            'authenticatedUser' => prepareForView($this->auth->getAuthenticatedUser($request)),
            'csrf' => $this->session->get($request, 'csrf'),
            'flashMessage' => $this->session->getFlash($request),
            'locale' => $locale,
        ], $variables);
    }
}
