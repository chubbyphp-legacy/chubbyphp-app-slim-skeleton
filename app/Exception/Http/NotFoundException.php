<?php

namespace SlimSkeleton\Exception\Http;

final class NotFoundException extends AbstractHttpException
{
    /**
     * @param string $class
     * @param string $id
     *
     * @return NotFoundException
     */
    public static function createForEntity(string $class, string $id): self
    {
        return new self(sprintf('Entity %s not found with id %s', $class, $id), 404);
    }
}
