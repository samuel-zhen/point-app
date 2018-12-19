<?php

namespace Point\Validation\Rules;

use Respect\Validation\Rules\AbstractRule;

class SameValue extends AbstractRule
{
    protected $comparison;

    public function __construct($comparison)
    {
        $this->comparison = $comparison;
    }

    public function validate($input)
    {
        return $this->comparison === $input;
    }
}