<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'sku', 'name', 'category_id', 'unit_id', 'supplier_id',
        'barcode', 'cost', 'price', 'reorder_point', 'description',
    ];

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

    // Total quantity on hand across all warehouses.
    public function getStockAttribute(): int
    {
        return (int) $this->batches->sum('quantity');
    }

    public function getStockValueAttribute(): float
    {
        return $this->stock * (float) $this->cost;
    }

    public function getStatusAttribute(): string
    {
        $stock = $this->stock;
        if ($stock <= 0) return 'out';
        if ($stock <= $this->reorder_point) return 'low';
        return 'in';
    }
}
