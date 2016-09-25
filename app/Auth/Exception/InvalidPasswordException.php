<?php

namespace SlimSkeleton\Auth\Exception;

final class InvalidPasswordException extends AbstractAuthException
{
    /**
     * @return InvalidPasswordException
     */
    public static function create(): self
    {
        return new self('Invalid password');
    }
}
