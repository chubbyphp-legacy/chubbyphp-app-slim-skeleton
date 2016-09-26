<?php

namespace SlimSkeleton\Translation;

final class TranslatorTwigExtension extends \Twig_Extension
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('trans', [$this, 'trans']),
        ];
    }

    /**
     * @param string $key
     * @param string $locale
     * @param array  $args
     *
     * @return string
     */
    public function trans(string $key, string $locale, array $args = []): string
    {
        return $this->translator->translate($locale, $key, $args);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'translator';
    }
}
