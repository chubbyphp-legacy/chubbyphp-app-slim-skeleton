<?php

namespace SlimSkeleton\Auth\Exception;

final class UserNotFoundException extends AbstractAuthException
{
    /**
     * @param string $email
     * @return UserNotFoundException
     */
    public static function create(string $email): self
    {
        return new self(sprintf('User not found with email %s', $email));
    }
}
