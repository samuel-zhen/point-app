<?php

namespace Point\Validation\Rules;

use Point\Models\User;
use Respect\Validation\Rules\AbstractRule;
use Illuminate\Database\Capsule\Manager as Capsule;

class UniqueExceptInitial extends AbstractRule
{
    protected $table;

    protected $column;

    protected $initial;

    public function __construct($table, $column, $initial)
    {
        $this->table = $table;
        $this->column = $column;
        $this->initial= $initial;
    }

    public function validate($input)
    {
        return Capsule::table($this->table)->where($this->column, $input)->doesntExist() || $this->initial === $input;
    }
}