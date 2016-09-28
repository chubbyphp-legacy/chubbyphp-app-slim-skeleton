<?php

namespace SlimSkeleton\Security\Exception;

final class UserNotFoundException extends AbstractLoginException
{
    /**
     * @param string $email
     *
     * @return UserNotFoundException
     */
    public static function create(string $email): self
    {
        return new self(sprintf('User not found with email %s', $email));
    }
}
