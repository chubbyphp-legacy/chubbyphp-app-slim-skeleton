<?php

declare(strict_types=1);

namespace SlimSkeleton\Deserialization;

use Chubbyphp\Deserialization\Mapping\ObjectMappingInterface;
use Chubbyphp\Deserialization\Mapping\PropertyMapping;
use Chubbyphp\Deserialization\Mapping\PropertyMappingInterface;
use SlimSkeleton\Search\UserSearch;

final class UserSearchMapping implements ObjectMappingInterface
{
    /**
     * @return string
     */
    public function getClass(): string
    {
        return UserSearch::class;
    }

    /**
     * @return callable
     */
    public function getFactory(): callable
    {
        return [UserSearch::class, 'create'];
    }

    /**
     * @return PropertyMappingInterface[]
     */
    public function getPropertyMappings(): array
    {
        return [
            new PropertyMapping('page'),
            new PropertyMapping('perPage'),
            new PropertyMapping('sort'),
            new PropertyMapping('order'),
        ];
    }
}
