<?php

namespace SlimSkeleton\Validation\Rules;

use Respect\Validation\Rules\AbstractRule;
use SlimSkeleton\Model\ModelInterface;
use SlimSkeleton\Repository\RepositoryInterface;

class UniqueModelRule extends AbstractRule
{
    /**
     * @var ModelInterface
     */
    private $model;

    /**
     * @var string
     */
    private $field;

    /**
     * @var RepositoryInterface
     */
    private $repository;

    /**
     * @param ModelInterface $model
     * @param string         $field
     */
    public function __construct(ModelInterface $model, string $field)
    {
        $this->model = $model;
        $this->field = $field;
        $this->setName($field);
    }

    /**
     * @return string
     */
    public function getModelClass(): string
    {
        return get_class($this->model);
    }

    /**
     * @param RepositoryInterface $repository
     */
    public function setRepository(RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param mixed $input
     *
     * @return bool
     */
    public function validate($input): bool
    {
        if (null === $this->repository) {
            throw new \RuntimeException(
                'Rule %s needs a repository of interface %s, please call setRepository before validate.',
                self::class,
                RepositoryInterface::class
            );
        }

        $model = $this->repository->findOneBy([$this->field => $input]);
        if (null !== $model && $model->getId() !== $this->model->getId()) {
            return false;
        }

        return true;
    }
}
