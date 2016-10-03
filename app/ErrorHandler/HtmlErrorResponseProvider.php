<?php

namespace SlimSkeleton\ErrorHandler;

use Chubbyphp\ErrorHandler\ErrorResponseProviderInterface;
use Chubbyphp\ErrorHandler\HttpException;
use Chubbyphp\Session\SessionInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use SlimSkeleton\Controller\Traits\TwigDataTrait;
use SlimSkeleton\Security\AuthInterface;

final class HtmlErrorResponseProvider implements ErrorResponseProviderInterface
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
     * @return string
     */
    public function getContentType(): string
    {
        return 'text/html';
    }

    /**
     * @param Request    $request
     * @param Response   $response
     * @param \Exception $exception
     *
     * @return Response
     */
    public function get(Request $request, Response $response, \Exception $exception): Response
    {
        if ($exception instanceof HttpException) {
            $request = $exception->getRequest();
            $response = $exception->getResponse();

            return $this->twig->render($response, '@SlimSkeleton/exception/http.html.twig',
                $this->getTwigData($request, ['code' => $exception->getCode(), 'message' => $exception->getMessage()])
            )->withStatus($exception->getCode());
        }

        return $this->twig->render($response, '@SlimSkeleton/exception/default.html.twig',
            $this->getTwigData($request, ['code' => 500, 'message' => $exception->getMessage()])
        )->withStatus(500);
    }
}
