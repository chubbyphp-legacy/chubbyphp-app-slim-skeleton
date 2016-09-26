<?php

namespace SlimSkeleton\Controller\Traits;

use Psr\Http\Message\ServerRequestInterface as Request;
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
        return array_replace_recursive([
            'authenticatedUser' => prepareForView($this->auth->getAuthenticatedUser($request)),
            'flashMessage' => $this->session->getFlash($request),
            'locale' => 'de',
        ], $variables);
    }
}
