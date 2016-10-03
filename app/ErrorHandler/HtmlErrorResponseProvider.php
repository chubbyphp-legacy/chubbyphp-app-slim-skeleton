<?php

namespace SlimSkeleton\ErrorHandler;

use Chubbyphp\ErrorHandler\ErrorResponseProviderInterface;
use Chubbyphp\ErrorHandler\HttpException;
use Chubbyphp\Session\SessionInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Http\Body;
use Slim\Views\Twig;
use SlimSkeleton\Controller\Traits\TwigDataTrait;
use SlimSkeleton\Security\AuthInterface;

final class HtmlErrorResponseProvider implements ErrorResponseProviderInterface
{
    use TwigDataTrait;

    /**
     * @var bool
     */
    private $displayErrorDetails;

    /**
     * @var Twig
     */
    private $twig;

    /**
     * @param AuthInterface    $auth
     * @param SessionInterface $session
     * @param Twig             $twig
     * @param bool             $displayErrorDetails
     */
    public function __construct(
        AuthInterface $auth,
        SessionInterface $session,
        Twig $twig,
        bool $displayErrorDetails = false
    ) {
        $this->auth = $auth;
        $this->displayErrorDetails = $displayErrorDetails;
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

            return $this->twig->render($response, '@SlimSkeleton/httpexception.html.twig',
                $this->getTwigData($request, ['code' => $exception->getCode(), 'message' => $exception->getMessage()])
            )->withStatus($exception->getCode());
        }

        $body = new Body(fopen('php://temp', 'r+'));
        $body->write($this->renderHtmlErrorMessage($exception));

        return $response
            ->withStatus(500)
            ->withHeader('Content-type', $this->getContentType())
            ->withBody($body);
    }

    /**
     * @link https://github.com/slimphp/Slim/blob/3.5.0/Slim/Handlers/Error.php#L73
     *
     * @param \Exception $exception
     *
     * @return string
     */
    private function renderHtmlErrorMessage(\Exception $exception): string
    {
        $title = 'Slim Application Error';

        if ($this->displayErrorDetails) {
            $html = '<p>The application could not run because of the following error:</p>';
            $html .= '<h2>Details</h2>';
            $html .= $this->renderHtmlException($exception);

            while ($exception = $exception->getPrevious()) {
                $html .= '<h2>Previous exception</h2>';
                $html .= $this->renderHtmlException($exception);
            }
        } else {
            $html = '<p>A website error has occurred. Sorry for the temporary inconvenience.</p>';
        }

        $output = sprintf(
            "<html><head><meta http-equiv='Content-Type' content='text/html; charset=utf-8'>".
            '<title>%s</title><style>body{margin:0;padding:30px;font:12px/1.5 Helvetica,Arial,Verdana,'.
            'sans-serif;}h1{margin:0;font-size:48px;font-weight:normal;line-height:48px;}strong{'.
            'display:inline-block;width:65px;}</style></head><body><h1>%s</h1>%s</body></html>',
            $title,
            $title,
            $html
        );

        return $output;
    }

    /**
     * @link https://github.com/slimphp/Slim/blob/3.5.0/Slim/Handlers/Error.php#L110
     *
     * @param \Exception $exception
     *
     * @return string
     */
    private function renderHtmlException(\Exception $exception): string
    {
        $html = sprintf('<div><strong>Type:</strong> %s</div>', get_class($exception));

        if (($code = $exception->getCode())) {
            $html .= sprintf('<div><strong>Code:</strong> %s</div>', $code);
        }

        if (($message = $exception->getMessage())) {
            $html .= sprintf('<div><strong>Message:</strong> %s</div>', htmlentities($message));
        }

        if (($file = $exception->getFile())) {
            $html .= sprintf('<div><strong>File:</strong> %s</div>', $file);
        }

        if (($line = $exception->getLine())) {
            $html .= sprintf('<div><strong>Line:</strong> %s</div>', $line);
        }

        if (($trace = $exception->getTraceAsString())) {
            $html .= '<h2>Trace</h2>';
            $html .= sprintf('<pre>%s</pre>', htmlentities($trace));
        }

        return $html;
    }
}
