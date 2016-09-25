<?php

namespace SlimSkeleton\Model;

interface ModelInterface extends \JsonSerializable
{
    /**
     * @param array $data
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
}
