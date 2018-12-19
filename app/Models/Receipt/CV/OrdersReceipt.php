<?php

namespace Point\Models\Receipt\CV;

use Point\Models\Receipt\CV\Receipt;
use Illuminate\Database\Eloquent\Model;

class OrdersReceipt extends Model
{
    protected $table = 'cv_receipt_orders';

    protected $fillable = ['work_order_id', 'receipt_id', 'quantity', 'unit', 'product', 'price', 'total'];

    public function receipt()
    {
        return $this->belongsTo(Receipt::class, 'receipt_id');
    }
}