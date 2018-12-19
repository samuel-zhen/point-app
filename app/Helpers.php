<?php

namespace Point;

class Helpers
{
    // If value is empty string, then return null
    public static function emptyToNull($value)
    {
        if (trim($value) === '') {
            return null;
        }

        return trim($value);
    }

    // Remove dot from number formatting
    public static function thousandFormat($value)
    {
        return str_replace('.', '', trim($value));
    }
}