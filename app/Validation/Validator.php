<?php

namespace SlimSkeleton\Validation;

use Respect\Validation\Exceptions\NestedValidationException;
use SlimSkeleton\Model\ModelInterface;

final class Validator implements ValidatorInterface
{
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
}
