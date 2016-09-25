<?php

namespace SlimSkeleton\Repository;

use SlimSkeleton\Model\ModelInterface;

interface RepositoryInterface
{
    /**
     * @param string $id
     * @return ModelInterface|null
     */
    public function find(string $id);

    /**
     * @return ModelInterface[]|array
     */
    public function findAll(): array;

    /**
     * @param ModelInterface $model
     */
    public function insert(ModelInterface $model);

    /**
     * @param ModelInterface $model
     */
    public function update(ModelInterface $model);
}
