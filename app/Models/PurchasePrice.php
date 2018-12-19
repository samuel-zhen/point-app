<?php

namespace Point\Models;

use Point\Models\ProductCategory;
use Illuminate\Database\Eloquent\Model;

class PurchasePrice extends Model
{
    protected $table = 'purchase_prices';

    protected $fillable = ['product_name', 'price', 'category_id'];

    public function productCategory()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }
}