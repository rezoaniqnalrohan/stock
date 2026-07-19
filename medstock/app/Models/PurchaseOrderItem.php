<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderItem extends Model
{
    protected $fillable = ['purchase_order_id', 'product_id', 'quantity', 'cost', 'lot_number', 'expiry_date'];

    protected $casts = ['expiry_date' => 'date'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
