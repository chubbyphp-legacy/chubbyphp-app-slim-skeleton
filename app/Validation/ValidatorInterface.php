<?php

namespace SlimSkeleton\Validation;

use SlimSkeleton\Model\ModelInterface;

interface ValidatorInterface
{
    /**
     * @param ModelInterface $model
     *
     * @return array
     */
    public function validateModel(ModelInterface $model): array;

    /**
     * @param array $data
     * @param array $validators
     *
     * @return array
     */
    public function validateArray(array $data, array $validators): array;
}
