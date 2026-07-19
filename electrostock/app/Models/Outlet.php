<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Outlet extends Model
{
    protected $guarded = [];

    public function stocks() { return $this->hasMany(Stock::class); }
    public function sales() { return $this->hasMany(Sale::class); }
}
