<?php

namespace Point\Models;

use Point\Models\User;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    protected $table = 'notes';

    protected $fillable = ['body', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}