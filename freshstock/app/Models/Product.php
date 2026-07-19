<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $guarded = [];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function batches()
    {
        return $this->hasMany(Batch::class);
    }

    // Total quantity on hand across all warehouses (sum of batch quantities).
    public function stockOnHand(): int
    {
        return (int) $this->batches()->sum('quantity');
    }

    public function isLowStock(): bool
    {
        return $this->stockOnHand() <= $this->reorder_point;
    }

    public function stockValue(): float
    {
        return $this->stockOnHand() * (float) $this->cost;
    }
}
