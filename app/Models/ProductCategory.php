<?php

namespace Point\Models;

use Point\Models\PurchasePrice;
use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    protected $table = 'product_categories';

    protected $fillable = ['name'];

    public function purchasePrices()
    {
        return $this->hasMany(PurchasePrice::class, 'category_id');
    }
}