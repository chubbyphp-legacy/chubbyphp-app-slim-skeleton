<?php

namespace SlimSkeleton\Controller\Traits;

use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Route;
use SlimSkeleton\Auth\AuthInterface;
use SlimSkeleton\Session\SessionInterface;

trait TwigDataTrait
{
    /**
     * @var AuthInterface
     */
    private $auth;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @param Request $request
     * @param array   $variables
     *
     * @return array
     */
    private function getTwigData(Request $request, array $variables = []): array
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
