<?php

namespace SlimSkeleton\Exception\Http;

final class BadRequestException extends AbstractHttpException
{
    /**
     * @param string $argument
     *
     * @return BadRequestException
     */
    public static function createForMissingArgument(string $argument): self
    {
        return new self(sprintf('Missing argument %s', $argument), 400);
    }
}
