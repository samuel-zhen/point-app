<?php

namespace Point\Models\Receipt\Store;

use Illuminate\Database\Eloquent\Model;
use Point\Models\Receipt\Store\Receipt;

class ItemsReceipt extends Model
{
    protected $table = 'store_receipt_items';

    protected $fillable = ['receipt_id', 'quantity', 'unit', 'product', 'price', 'total'];

    public function receipt()
    {
        return $this->belongsTo(Receipt::class);
    }
}