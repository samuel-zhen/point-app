<?php

namespace Point\Models;

use Illuminate\Database\Eloquent\Model;

class SellPrice extends Model
{
    protected $table = 'sell_prices';

    protected $fillable = ['product_name', 'price'];
}