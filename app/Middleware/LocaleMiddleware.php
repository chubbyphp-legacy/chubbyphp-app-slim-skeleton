<?php

namespace SlimSkeleton\Middleware;

use Negotiation\AcceptLanguage;
use Negotiation\LanguageNegotiator;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

final class LocaleMiddleware
{
    /**
     * @var LanguageNegotiator
     */
    private $languageNegotiator;

    /**
     * @var string
     */
    private $localeFallback;

    /**
     * @var array
     */
    private $locales;

    /**
     * @param LanguageNegotiator $languageNegotiator
     * @param string             $localeFallback
     * @param array              $locales
     */
    public function __construct(LanguageNegotiator $languageNegotiator, string $localeFallback, array $locales)
    {
        $this->languageNegotiator = $languageNegotiator;
        $this->localeFallback = $localeFallback;
        $this->locales = $locales;
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
        if ('/' === $request->getUri()->getPath()) {
            $acceptLanguageHeader = $request->getHeaderLine('Accept-Language');
            /** @var AcceptLanguage $acceptLanguage */
            if (null !== $acceptLanguage = $this->languageNegotiator->getBest($acceptLanguageHeader, $this->locales)) {
                $locale = $acceptLanguage->getType();
            } else {
                $locale = $this->localeFallback;
            }

            return $response->withStatus(302)->withHeader('Location', '/'.$locale);
        }

        $response = $next($request, $response);

        return $response;
    }
}
