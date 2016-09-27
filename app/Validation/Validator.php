<?php

namespace SlimSkeleton\Validation;

use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validatable;
use SlimSkeleton\Model\ModelInterface;
use SlimSkeleton\Repository\RepositoryInterface;
use SlimSkeleton\Validation\Rules\UniqueModelRule;

final class Validator implements ValidatorInterface
{
    /**
     * @var RepositoryInterface[]|array
     */
    private $repositories = [];

    /**
     * @param RepositoryInterface[]|array $repositories
     */
    public function __construct(array $repositories)
    {
        foreach ($repositories as $repository) {
            $this->addRepository($repository);
        }
    }

    /**
     * @param RepositoryInterface $repository
     */
    private function addRepository(RepositoryInterface $repository)
    {
        $this->repositories[$repository->getModelClass()] = $repository;
    }

    /**
     * @param ModelInterface $model
     *
     * @return array
     */
    public function validateModel(ModelInterface $model): array
    {
        $reflectionClass = new \ReflectionObject($model);

        $errorMessages = [];
        foreach ($model->getValidators() as $field => $validator) {
            try {
                $this->assignRepositoryToRules($validator->getRules());
                $reflectionProperty = $reflectionClass->getProperty($field);
                $reflectionProperty->setAccessible(true);
                $validator->assert($reflectionProperty->getValue($model));
            } catch (NestedValidationException $exception) {
                $errorMessages[$field] = $exception->getMessages();
            }
        }

        return $errorMessages;
    }

    /**
     * @param array $data
     * @param array $validators
     *
     * @return array
     */
    public function validateArray(array $data, array $validators): array
    {
        $errorMessages = [];
        foreach ($validators as $key => $validator) {
            try {
                $validator->assert($data[$key]);
            } catch (NestedValidationException $exception) {
                $errorMessages[$key] = $exception->getMessages();
            }
        }

        return $errorMessages;
    }

    /**
     * @param array $rules
     */
    private function assignRepositoryToRules(array $rules)
    {
        foreach ($rules as $rule) {
            $this->assignRepositoryToRule($rule);
        }
    }

    /**
     * @param Validatable $rule
     */
    private function assignRepositoryToRule(Validatable $rule)
    {
        if ($rule instanceof UniqueModelRule && isset($this->repositories[$rule->getModelClass()])) {
            $rule->setRepository($this->repositories[$rule->getModelClass()]);
        }
    }
}
