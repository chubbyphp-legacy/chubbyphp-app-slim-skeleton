<?php

namespace SlimSkeleton\Repository;

use SlimSkeleton\Model\ModelInterface;

interface RepositoryInterface
{
    /**
     * @param string $id
     *
     * @return ModelInterface|null
     */
    public function find(string $id);

    /**
     * @param array $criteria
     *
     * @return ModelInterface|null
     */
    public function findOneBy(array $criteria = []);

    /**
     * @param array $criteria
     *
     * @return ModelInterface[]array
     */
    public function findBy(array $criteria = []): array;

    /**
     * @param ModelInterface $model
     */
    public function insert(ModelInterface $model);

    /**
     * @param ModelInterface $model
     */
    public function update(ModelInterface $model);

    /**
     * @param ModelInterface $model
     */
    public function delete(ModelInterface $model);
}
