<?php

declare(strict_types=1);

namespace SlimSkeleton\Repository;

use Chubbyphp\Model\ModelInterface;
use SlimSkeleton\Model\User;

final class UserRepository extends AbstractRepository
{
    /**
     * @param string $modelClass
     *
     * @return bool
     */
    public function isResponsible(string $modelClass): bool
    {
        return $modelClass === User::class;
    }

    /**
     * @param array $row
     *
     * @return ModelInterface
     */
    protected function fromPersistence(array $row): ModelInterface
    {
        return User::fromPersistence($row);
    }

    /**
     * @return string
     */
    protected function getTable(): string
    {
        return 'users';
    }
}
