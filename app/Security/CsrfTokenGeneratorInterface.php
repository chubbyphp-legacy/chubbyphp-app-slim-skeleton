<?php

namespace SlimSkeleton\Security;

interface CsrfTokenGeneratorInterface
{
    /**
     * @return string
     */
    public function generate(): string;
}
