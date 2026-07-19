<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    protected $guarded = [];
    protected $casts = ['received_at' => 'datetime'];

    public function supplier() { return $this->belongsTo(Supplier::class); }
    public function outlet() { return $this->belongsTo(Outlet::class); }
    public function items() { return $this->hasMany(PurchaseOrderItem::class); }
}
