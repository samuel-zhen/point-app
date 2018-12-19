<?php

namespace Point\Models;

use Point\Models\User;
use Illuminate\Database\Eloquent\Model;

class AccessLevel extends Model
{
    protected $table = 'access_levels';

    public function users()
    {
        return $this->hasMany(User::class);
    }
}