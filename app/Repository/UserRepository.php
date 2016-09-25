<?php

namespace SlimSkeleton\Repository;

use SlimSkeleton\Model\User;

final class UserRepository extends AbstractDoctrineRepository
{
    /**
     * @return string
     */
    protected function getTablename(): string
    {
        return 'users';
    }

    /**
     * @return string
     */
    protected function getModelClass(): string
    {
        return User::class;
    }
}
