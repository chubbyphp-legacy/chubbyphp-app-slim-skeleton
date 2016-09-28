<?php

namespace SlimSkeleton\Security;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;
use SlimSkeleton\Controller\Traits\RenderErrorTrait;
use SlimSkeleton\Controller\Traits\TwigDataTrait;
use SlimSkeleton\Session\SessionInterface;

class CsrfTokenMiddleware
{
    use RenderErrorTrait;
    use TwigDataTrait;

    /**
     * @var CsrfTokenGeneratorInterface
     */
    private $csrfTokenGenerator;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @param AuthInterface               $auth
     * @param CsrfTokenGeneratorInterface $csrfTokenGenerator
     * @param SessionInterface            $session
     * @param Twig                        $twig
     */
    public function __construct(
        AuthInterface $auth,
        CsrfTokenGeneratorInterface $csrfTokenGenerator,
        SessionInterface $session,
        Twig $twig
    ) {
        $this->auth = $auth;
        $this->csrfTokenGenerator = $csrfTokenGenerator;
        $this->session = $session;
        $this->twig = $twig;
    }

    /**
     * @param Request  $request
     * @param Response $response
     * @param callable $next
     *
     * @return Response
     */
    public function __invoke(Request $request, Response $response, callable $next)
    {
        if (in_array($request->getMethod(), ['POST', 'PUT', 'DELETE', 'PATCH'])) {
            if (!$this->checkCsrf($request)) {
                return $this->renderError($request, $response, 424);
            }
        }

        if (!$this->session->has($request, 'csrf')) {
            $this->session->set($request, 'csrf', $this->csrfTokenGenerator->generate());
        }

        $response = $next($request, $response);

        return $response;
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    private function checkCsrf(Request $request): bool
    {
        if (!$this->session->has($request, 'csrf')) {
            return false;
        }

        $data = $request->getParsedBody();

        if (!isset($data['csrf'])) {
            return false;
        }

        if ($this->session->get($request, 'csrf') !== $data['csrf']) {
            return false;
        }

        return true;
    }
}
