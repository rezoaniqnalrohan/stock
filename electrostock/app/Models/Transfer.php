<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
    protected $guarded = [];
    protected $casts = ['received_at' => 'datetime'];

    public function product() { return $this->belongsTo(Product::class); }
    public function fromOutlet() { return $this->belongsTo(Outlet::class, 'from_outlet_id'); }
    public function toOutlet() { return $this->belongsTo(Outlet::class, 'to_outlet_id'); }
}
