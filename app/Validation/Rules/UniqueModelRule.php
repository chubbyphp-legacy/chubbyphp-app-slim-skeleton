<?php

namespace SlimSkeleton\Validation\Rules;

use Respect\Validation\Rules\AbstractRule;
use SlimSkeleton\Model\ModelInterface;
use SlimSkeleton\Repository\RepositoryInterface;

class UniqueModelRule extends AbstractRule
{
    /**
     * @var string[]|array
     */
    protected $properties; // needs to be protected (copy within exception)

    /**
     * @var RepositoryInterface
     */
    private $repository;

    /**
     * @param ModelInterface $model
     * @param string[]|array $properties
     */
    public function __construct(ModelInterface $model, array $properties)
    {
        $this->properties = $properties;
        $this->setName(implode(', ', $properties));
    }

    /**
     * @param RepositoryInterface $repository
     */
    public function setRepository(RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param ModelInterface $model
     *
     * @return bool
     */
    public function validate($model): bool
    {
        if (null === $this->repository) {
            throw new \RuntimeException(
                'Rule %s needs a repository of interface %s, please call setRepository before validate.',
                self::class,
                RepositoryInterface::class
            );
        }

        $reflectionClass = new \ReflectionObject($model);

        $criteria = [];
        foreach ($this->properties as $property) {
            $reflectionProperty = $reflectionClass->getProperty($property);
            $reflectionProperty->setAccessible(true);
            $criteria[$property] = $reflectionProperty->getValue($model);
        }

        $modelFromRepository = $this->repository->findOneBy($criteria);
        if (null !== $modelFromRepository && $modelFromRepository->getId() !== $model->getId()) {
            return false;
        }

        return true;
    }
}
