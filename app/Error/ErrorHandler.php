<?php

namespace SlimSkeleton\Error;

use Chubbyphp\ErrorHandler\ErrorHandlerInterface;
use Chubbyphp\Session\SessionInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use SlimSkeleton\Controller\Traits\TwigDataTrait;
use SlimSkeleton\Security\AuthInterface;

final class ErrorHandler implements ErrorHandlerInterface
{
    use TwigDataTrait;

    /**
     * @var Twig
     */
    private $twig;

    /**
     * ErrorHandler constructor.
     *
     * @param AuthInterface    $auth
     * @param SessionInterface $session
     * @param Twig             $twig
     */
    public function __construct(AuthInterface $auth, SessionInterface $session, Twig $twig)
    {
        $this->auth = $auth;
        $this->session = $session;
        $this->twig = $twig;
    }

    /**
     * @param Request  $request
     * @param Response $response
     * @param int      $statusCode
     *
     * @return Response
     */
    public function error(Request $request, Response $response, int $statusCode): Response
    {
        return $this->twig->render($response, '@SlimSkeleton/error.html.twig',
            $this->getTwigData($request, ['code' => $statusCode])
        )->withStatus($statusCode);
    }
}
