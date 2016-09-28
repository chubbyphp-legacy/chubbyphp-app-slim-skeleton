<?php

namespace SlimSkeleton\Security\Exception;

final class EmptyPasswordException extends \Exception
{
    /**
     * @return EmptyPasswordException
     */
    public static function create(): self
    {
        return new self('Empty password');
    }
}
