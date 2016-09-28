<?php

namespace SlimSkeleton\Auth;

final class CsrfTokenGenerator implements CsrfTokenGeneratorInterface
{
    /**
     * @var int
     */
    private $entropy;

    /**
     * @param int $entropy
     */
    public function __construct(int $entropy = 256)
    {
        $this->entropy = $entropy;
    }

    /**
     * @return string
     */
    public function generate(): string
    {
        $bytes = random_bytes($this->entropy / 8);

        return rtrim(strtr(base64_encode($bytes), '+/', '-_'), '=');
    }
}
