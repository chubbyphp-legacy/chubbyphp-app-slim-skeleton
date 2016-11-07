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
     * @var bool
     */
    private $debug;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var array
     */
    private $trail;

    /**
     * @param AuthenticationInterface $authentication
     * @param bool                    $debug
     * @param SessionInterface        $session
     * @param array                   $trail
     */
    public function __construct(
        AuthenticationInterface $authentication,
        bool $debug,
        SessionInterface $session,
        array $trail
    ) {
        $this->authentication = $authentication;
        $this->debug = $debug;
        $this->session = $session;
        $this->trail = $trail;
    }

    /**
     * @param Request $request
     * @param array   $variables
     *
     * @return array
     */
    public function aggregate(Request $request, array $variables = []): array
    {
        /* @var Route $route */
        if (null === $route = $request->getAttribute('route')) {
            throw new \RuntimeException('The route has to be resolved');
        }

        if (null === $locale = $request->getAttribute('locale')) {
            $locale = $route->getArgument('locale');
        }

        return array_replace_recursive([
            'authenticatedUser' => prepareForView($this->authentication->getAuthenticatedUser($request)),
            'csrf' => $this->session->get($request, CsrfMiddleware::CSRF_KEY),
            'debug' => $this->debug,
            'flashMessage' => $this->session->getFlash($request),
            'locale' => $locale,
            'trail' => $this->getTrailForRoute($route),
        ], $variables);
    }

    /**
     * @param Route $route
     *
     * @return array
     */
    private function getTrailForRoute(Route $route): array
    {
        $routeName = $route->getName();

        if (!isset($this->trail[$routeName])) {
            return [];
        }

        return array_merge([$routeName], $this->trail[$routeName]);
    }
}
