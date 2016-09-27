<?php

namespace SlimSkeleton\Model;

use Respect\Validation\Validator;

interface ModelInterface extends \JsonSerializable
{
    /**
     * @param array $data
     *
     * @return ModelInterface
     */
    public static function fromRow(array $data): ModelInterface;

    /**
     * @return array
     */
    public function toRow(): array;

    /**
     * @return string
     */
    public function getId(): string;

    /**
     * @return Validator|null
     */
    public function getModelValidator();

    /**
     * @return Validator[]|array
     */
    public function getPropertyValidators(): array;
}
