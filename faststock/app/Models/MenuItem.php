<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    protected $guarded = [];

    public function ingredients()
    {
        return $this->belongsToMany(Ingredient::class)->withPivot('qty');
    }

    // How many of this item current ingredient stock can make.
    public function available(): int
    {
        return (int) $this->ingredients->map(fn ($i) => $i->pivot->qty > 0 ? floor($i->stock / $i->pivot->qty) : PHP_INT_MAX)->min();
    }
}
