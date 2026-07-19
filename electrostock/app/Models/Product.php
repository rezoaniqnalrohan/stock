<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $guarded = [];

    public function brand() { return $this->belongsTo(Brand::class); }
    public function category() { return $this->belongsTo(Category::class); }
    public function stocks() { return $this->hasMany(Stock::class); }

    public function totalStock(): int
    {
        return (int) $this->stocks()->sum('quantity');
    }
}
