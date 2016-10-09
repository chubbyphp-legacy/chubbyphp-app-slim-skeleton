<?php

namespace SlimSkeleton\Service;

use Chubbyphp\Csrf\CsrfMiddleware;
use Chubbyphp\Security\Authentication\AuthenticationInterface;
use Chubbyphp\Session\SessionInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Route;

final class TemplateData
{
    /**
     * @var AuthenticationInterface
     */
    private $authentication;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @param AuthenticationInterface $authentication
     * @param SessionInterface        $session
     */
    public function __construct(AuthenticationInterface $authentication, SessionInterface $session)
    {
        $this->authentication = $authentication;
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
            'authenticatedUser' => prepareForView($this->authentication->getAuthenticatedUser($request)),
            'csrf' => $this->session->get($request, CsrfMiddleware::CSRF_KEY),
            'flashMessage' => $this->session->getFlash($request),
            'locale' => $locale,
        ], $variables);
    }
}