<?php

namespace SlimSkeleton\Validation\Exceptions;

use Respect\Validation\Exceptions\ValidationException;

class UniqueModelRuleException extends ValidationException
{
    public static $defaultTemplates = [
        self::MODE_DEFAULT => [
            self::STANDARD => 'Field {{name}} is not unique',
        ],
        self::MODE_NEGATIVE => [
            self::STANDARD => 'Field {{name}} is  unique',
        ],
    ];
}
