<?php

namespace Point\Validation\Exceptions;

use Respect\Validation\Exceptions\ValidationException;

class UniqueExceptInitialException extends ValidationException
{
    public static $defaultTemplates = [
        self::MODE_DEFAULT => [
            self::STANDARD => '{{name}} is already taken.',
        ],
    ];
}