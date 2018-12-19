<?php

namespace Point\Models;

use Point\Models\AccessLevel;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table = 'customers';

    protected $fillable = ['name', 'phone_number', 'company', 'address'];
}