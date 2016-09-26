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
            new \Twig_SimpleFilter('textToTransKey', [$this, 'textToTransKey']),
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
     * @param string $string
     *
     * @return string
     */
    public function textToTransKey(string $string): string
    {
        $string = strtolower($string);
        $string = preg_replace('/[^a-zA-Z0-9]/i', '', $string);

        return $string;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'translator';
    }
}
