<?php

namespace Point\Models\Receipt\CV;

use Point\Models\Receipt\CV\Receipt;
use Illuminate\Database\Eloquent\Model;

class ItemsReceipt extends Model
{
    protected $table = 'cv_receipt_items';

    protected $fillable = ['receipt_id', 'quantity', 'unit', 'product', 'price', 'total'];

    public function receipt()
    {
        return $this->belongsTo(Receipt::class);
    }
}