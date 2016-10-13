<?php

namespace SlimSkeleton\ErrorHandler;

use Chubbyphp\ErrorHandler\ErrorResponseProviderInterface;
use Chubbyphp\ErrorHandler\HttpException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Handlers\Error;
use Slim\Views\Twig;
use SlimSkeleton\Service\TemplateData;

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
     * @var Twig
     */
    private $twig;

    /**
     * @param Error        $fallbackErrorHandler
     * @param TemplateData $templateData
     * @param Twig         $twig
     */
    public function __construct(Error $fallbackErrorHandler, TemplateData $templateData, Twig $twig)
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
                        'message' => !$this->isDefaultMessage($exception) ? $exception->getMessage() : '',
                    ]
                )
            )->withStatus($exception->getCode());
        }

        $fallbackErrorHandler = $this->fallbackErrorHandler;

        return $fallbackErrorHandler($request, $response, $exception);
    }

    private function isDefaultMessage(HttpException $exception): bool
    {
        $constantName = 'STATUS_'.$exception->getCode();
        $reflection = new \ReflectionObject($exception);
        if ($reflection->hasConstant($constantName)) {
            $defaultMessage = $reflection->getConstant($constantName);
        } else {
            $defaultMessage = 'unknown';
        }

        return $exception->getMessage() === $defaultMessage;
    }
}
