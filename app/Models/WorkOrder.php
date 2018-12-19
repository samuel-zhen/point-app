<?php

namespace Point\Models;

use Point\Models\User;
use Illuminate\Database\Eloquent\Model;

class WorkOrder extends Model
{
    protected $table = 'work_orders';

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function status()
    {
        if ($this->is_complete) {
            return 'Completed';
        } else {
            return 'Ongoing';
        }
    }
}