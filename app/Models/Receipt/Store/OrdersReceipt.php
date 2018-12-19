<?php

namespace Point\Models\Receipt\Store;

use Illuminate\Database\Eloquent\Model;
use Point\Models\Receipt\Store\Receipt;

class OrdersReceipt extends Model
{
    protected $table = 'store_receipt_orders';

    protected $fillable = ['work_order_id', 'receipt_id', 'quantity', 'unit', 'product', 'price', 'total'];

    public function receipt()
    {
        return $this->belongsTo(Receipt::class, 'receipt_id');
    }
}