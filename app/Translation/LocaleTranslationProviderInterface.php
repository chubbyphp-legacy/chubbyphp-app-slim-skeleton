<?php

namespace SlimSkeleton\Translation;

interface LocaleTranslationProviderInterface
{
    /**
     * @return string
     */
    public function getLocale(): string;

    /**
     * @param string $key
     * @param array $args
     * @return string
     */
    public function translate(string $key, array $args): string;
}
