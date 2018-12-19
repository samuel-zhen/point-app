<?php

namespace Point\Models;

use Point\Models\WorkOrder;
use Point\Models\AccessLevel;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'users';

    protected $fillable = ['username', 'password', 'access_level_id'];

    public function accessLevel()
    {
        return $this->belongsTo(AccessLevel::class);
    }

    public function workOrders()
    {
        return $this->hasMany(WorkOrder::class, 'user_id');
    }

    public function setPassword($password)
    {
        $this->update([
            'password' => password_hash($password, PASSWORD_DEFAULT),
        ]);
    }
}