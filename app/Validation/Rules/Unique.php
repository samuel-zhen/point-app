<?php

namespace Point\Validation\Rules;

use Point\Models\User;
use Respect\Validation\Rules\AbstractRule;
use Illuminate\Database\Capsule\Manager as Capsule;

class Unique extends AbstractRule
{
    protected $table;

    protected $column;

    public function __construct($table, $column)
    {
        $this->table = $table;
        $this->column = $column;
    }

    public function validate($input)
    {
        return Capsule::table($this->table)->where($this->column, $input)->doesntExist();
    }
}