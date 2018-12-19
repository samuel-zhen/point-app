<?php

namespace Point\Models\Receipt\Store;

use Point\Models\User;
use Point\Models\WorkOrder;
use Illuminate\Database\Eloquent\Model;
use Point\Models\Receipt\Store\ItemsReceipt;
use Point\Models\Receipt\Store\OrdersReceipt;

class Receipt extends Model
{
    protected $table = 'store_receipts';

    protected $fillable = ['user_id', 'customer', 'total', 'is_settled', 'settled_date', 'is_deleted'];

    public function formattedNumber()
    {
       if (strlen($this->id) < 5) {
            $zeroes = 5 - strlen($this->id);
            return str_repeat('0', $zeroes) . $this->id;
       } else {
           return $this->id;
       }
    }

    public function workOrders()
    {
        return $this->hasMany(OrdersReceipt::class, 'receipt_id');
    }

    public function items()
    {
        return $this->hasMany(ItemsReceipt::class, 'receipt_id');
    }

    public function works()
    {
        return $this->hasManyThrough(WorkOrder::class, OrdersReceipt::class, 'receipt_id', 'id', 'id', 'work_order_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function status()
    {
        return $this->is_settled ? 'Lunas' : 'Hutang';
    }
}