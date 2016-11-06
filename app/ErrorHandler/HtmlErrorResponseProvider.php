<?php

namespace SlimSkeleton\ErrorHandler;

use Chubbyphp\ErrorHandler\ErrorResponseProviderInterface;
use Chubbyphp\ErrorHandler\HttpException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Handlers\Error;
use SlimSkeleton\Service\TemplateData;
use SlimSkeleton\Service\TwigRender;

final class HtmlErrorResponseProvider implements ErrorResponseProviderInterface
{
    /**
     * @var Error
     */
    private $fallbackErrorHandler;

    /**
     * @var TemplateData
     */
    private $templateData;

    /**
     * @var TwigRender
     */
    private $twig;

    /**
     * @param Error        $fallbackErrorHandler
     * @param TemplateData $templateData
     * @param TwigRender   $twig
     */
    public function __construct(Error $fallbackErrorHandler, TemplateData $templateData, TwigRender $twig)
    {
        $this->fallbackErrorHandler = $fallbackErrorHandler;
        $this->templateData = $templateData;
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
                $this->templateData->aggregate(
                    $request,
                    [
                        'code' => $exception->getCode(),
                        'message' => !$exception->hasDefaultMessage() ? $exception->getMessage() : '',
                    ]
                )
            )->withStatus($exception->getCode());
        }

        $fallbackErrorHandler = $this->fallbackErrorHandler;

        return $fallbackErrorHandler($request, $response, $exception);
    }
}
