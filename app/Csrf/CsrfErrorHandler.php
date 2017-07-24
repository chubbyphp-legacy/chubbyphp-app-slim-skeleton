<?php

declare(strict_types=1);

namespace SlimSkeleton\Csrf;

use Chubbyphp\Csrf\CsrfErrorHandlerInterface;
use Chubbyphp\Session\FlashMessage;
use Chubbyphp\Session\SessionInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Http\Uri;

final class CsrfErrorHandler implements CsrfErrorHandlerInterface
{
    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @param SessionInterface $session
     */
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @param Request  $request
     * @param Response $response
     * @param int      $code
     * @param string   $reasonPhrase
     *
     * @return Response
     */
    public function errorResponse(Request $request, Response $response, int $code, string $reasonPhrase): Response
    {
        $this->session->addFlash($request, new FlashMessage(FlashMessage::TYPE_DANGER, $reasonPhrase));

        $uri = $request->getUri();
        $refererUri = Uri::createFromString($request->getHeaderLine('Referer'));

        if ($uri->getHost() === $refererUri->getHost()) {
            $location = (string) $refererUri;
        } else {
            $location = (string) $uri->withPath('/')->withQuery('')->withFragment('');
        }

        return $response->withStatus(302)->withHeader('Location', $location);
    }
}
