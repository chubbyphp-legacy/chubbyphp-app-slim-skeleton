<?php

declare(strict_types=1);

namespace SlimSkeleton\Service;

use Chubbyphp\Csrf\CsrfErrorResponseMiddleware;
use Chubbyphp\Security\Authentication\AuthenticationInterface;
use Chubbyphp\Session\SessionInterface;
use Chubbyphp\Translation\TranslatorInterface;
use Chubbyphp\Validation\Error\NestedErrorMessages;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Route;

final class TwigRender
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
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @param AuthenticationInterface $authentication
     * @param bool                    $debug
     * @param SessionInterface        $session
     * @param array                   $trail
     * @param TranslatorInterface     $translator
     */
    public function __construct(
        AuthenticationInterface $authentication,
        bool $debug,
        SessionInterface $session,
        array $trail,
        TranslatorInterface $translator,
        \Twig_Environment $twig
    ) {
        $this->authentication = $authentication;
        $this->debug = $debug;
        $this->session = $session;
        $this->trail = $trail;
        $this->translator = $translator;
        $this->twig = $twig;
    }

    /**
     * @param Response $response
     * @param $name
     * @param array $context
     *
     * @return Response
     */
    public function render(Response $response, $name, array $context = []): Response
    {
        $response->getBody()->write($this->twig->render($name, $context));

        return $response;
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
            'csrf' => $this->session->get($request, CsrfErrorResponseMiddleware::CSRF_KEY),
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

    /**
     * @param string $locale
     * @param array  $errors
     *
     * @return array
     */
    public function getErrorMessages(string $locale, array $errors): array
    {
        $translate = function (string $key, array $args) use ($locale) {
            return $this->translator->translate($locale, $key, $args);
        };

        return (new NestedErrorMessages($errors, $translate))->getMessages();
    }
}
