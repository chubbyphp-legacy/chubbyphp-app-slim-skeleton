<?php

declare(strict_types=1);

namespace SlimSkeleton\Repository;

use Chubbyphp\Model\Doctrine\DBAL\Repository\AbstractDoctrineRepository;

abstract class AbstractRepository extends AbstractDoctrineRepository
{
    /**
     * @param string $id
     * @param array  $row
     */
    protected function insert(string $id, array $row)
    {
        $row['createdAt'] = (new \DateTime())->format('Y-m-d H:i:s');

        parent::insert($id, $row);
    }

    /**
     * @param string $id
     * @param array  $row
     */
    protected function update(string $id, array $row)
    {
        $row['updatedAt'] = (new \DateTime())->format('Y-m-d H:i:s');

        parent::update($id, $row);
    }
}
