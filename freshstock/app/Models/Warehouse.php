<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    protected $guarded = [];

    protected $casts = ['is_cold_chain' => 'boolean'];

    public function batches()
    {
        return $this->hasMany(Batch::class);
    }
}
