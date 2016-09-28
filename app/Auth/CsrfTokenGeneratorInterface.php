<?php

namespace SlimSkeleton\Auth;

interface CsrfTokenGeneratorInterface
{
    /**
     * @return string
     */
    public function generate(): string;
}
