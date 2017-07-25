<?php

declare(strict_types=1);

namespace SlimSkeleton\Repository;

use Chubbyphp\Model\ModelInterface;
use SlimSkeleton\Model\User;
use SlimSkeleton\Search\UserSearch;

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

    /**
     * @param UserSearch $search
     *
     * @return UserSearch
     */
    public function search(UserSearch $search): UserSearch
    {
        $criteria = [];
        $orderBy = [$search->getSort() => $search->getOrder()];
        $limit = $search->getPerPage();
        $offset = $search->getPage() * $search->getPerPage() - $search->getPerPage();

        $search->setElements($this->findBy($criteria, $orderBy, $limit, $offset));
        $search->setElementCount($this->countBy($criteria));

        return $search;
    }
}
