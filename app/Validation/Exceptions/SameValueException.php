<?php

namespace Point\Validation\Exceptions;

use Respect\Validation\Exceptions\ValidationException;

class SameValueException extends ValidationException
{
    public static $defaultTemplates = [
        self::MODE_DEFAULT => [
            self::STANDARD => '{{name}} does not match.',
        ],
    ];
}