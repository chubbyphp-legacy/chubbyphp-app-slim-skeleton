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
        $errorMessagesFromProperties = $this->assertModelProperties($model);
        $errorMessagesFromModel = $this->assertValidateModel($model);

        return array_merge_recursive($errorMessagesFromProperties, $errorMessagesFromModel);
    }

    /**
     * @return array
     */
    private function assertModelProperties(ModelInterface $model): array
    {
        $reflectionClass = new \ReflectionObject($model);

        $errorMessages = [];
        foreach ($model->getPropertyValidators() as $field => $validator) {
            try {
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
     * @param ModelInterface $model
     *
     * @return array
     */
    private function assertValidateModel(ModelInterface $model): array
    {
        if (null === $modelValidator = $model->getModelValidator()) {
            return [];
        }

        $errorMessages = [];

        try {
            $this->assignRepositoryToRules(get_class($model), $modelValidator->getRules());
            $modelValidator->assert($model);
        } catch (NestedValidationException $exception) {
            foreach ($exception as $ruleException) {
                if ($ruleException->hasParam('properties')) {
                    foreach ($ruleException->getParam('properties') as $property) {
                        if (!isset($errorMessages[$property])) {
                            $errorMessages[$property] = [];
                        }
                        $errorMessages[$property][] = $ruleException->getMainMessage();
                    }
                } else {
                    if (!isset($errorMessages['__model'])) {
                        $errorMessages['__model'] = [];
                    }
                    $errorMessages['__model'] = $ruleException->getMainMessage();
                }
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
     * @param string $modelClass
     * @param array  $rules
     */
    private function assignRepositoryToRules(string $modelClass, array $rules)
    {
        foreach ($rules as $rule) {
            $this->assignRepositoryToRule($modelClass, $rule);
        }
    }

    /**
     * @param string      $modelClass
     * @param Validatable $rule
     */
    private function assignRepositoryToRule(string $modelClass, Validatable $rule)
    {
        if ($rule instanceof UniqueModelRule && isset($this->repositories[$modelClass])) {
            $rule->setRepository($this->repositories[$modelClass]);
        }
    }
}
