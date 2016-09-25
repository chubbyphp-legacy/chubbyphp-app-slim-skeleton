<?php

namespace SlimSkeleton\Repository;

use Doctrine\DBAL\Connection;
use SlimSkeleton\Model\ModelInterface;

abstract class AbstractDoctrineRepository implements RepositoryInterface
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param string $id
     *
     * @return ModelInterface|null
     */
    public function find(string $id)
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select('*')->from($this->getTablename())->where($qb->expr()->eq('id', ':id'))->setParameter('id', $id);

        $row = $qb->execute()->fetch(\PDO::FETCH_ASSOC);
        if (false === $row) {
            return null;
        }

        /** @var ModelInterface $modelClass */
        $modelClass = $this->getModelClass();

        return $modelClass::fromRow($row);
    }

    /**
     * @param array $criteria
     *
     * @return null|ModelInterface
     */
    public function findOneBy(array $criteria = [])
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select('*')->from($this->getTablename())->setMaxResults(1);

        foreach ($criteria as $field => $value) {
            $qb->andWhere($qb->expr()->eq($field, ':'.$field));
            $qb->setParameter($field, $value);
        }

        $row = $qb->execute()->fetch(\PDO::FETCH_ASSOC);
        if (false === $row) {
            return null;
        }

        /** @var ModelInterface $modelClass */
        $modelClass = $this->getModelClass();

        return $modelClass::fromRow($row);
    }

    /**
     * @param array $criteria
     *
     * @return ModelInterface[]|array
     */
    public function findBy(array $criteria = []): array
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select('*')->from($this->getTablename());

        foreach ($criteria as $field => $value) {
            $qb->andWhere($qb->expr()->eq($field, ':'.$field));
            $qb->setParameter($field, $value);
        }

        $rows = $qb->execute()->fetchAll(\PDO::FETCH_ASSOC);

        if ([] === $rows) {
            return [];
        }

        /** @var ModelInterface $modelClass */
        $modelClass = $this->getModelClass();

        $models = [];
        foreach ($rows as $row) {
            $models[] = $modelClass::fromRow($row);
        }

        return $models;
    }

    /**
     * @param ModelInterface $model
     */
    public function insert(ModelInterface $model)
    {
        $this->connection->insert($this->getTablename(), $model->toRow());
    }

    /**
     * @param ModelInterface $model
     */
    public function update(ModelInterface $model)
    {
        $this->connection->update($this->getTablename(), $model->toRow(), ['id' => $model->getId()]);
    }

    /**
     * @param ModelInterface $model
     */
    public function delete(ModelInterface $model)
    {
        $this->connection->delete($this->getTablename(), ['id' => $model->getId()]);
    }

    /**
     * @return string
     */
    abstract protected function getTablename(): string;

    /**
     * @return string
     */
    abstract protected function getModelClass(): string;
}
