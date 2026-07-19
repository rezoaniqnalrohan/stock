<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    protected $guarded = [];

    protected $casts = ['expiry_date' => 'date'];

    public function ingredient()
    {
        return $this->belongsTo(Ingredient::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Every stock change goes through here so ingredients.stock always matches the ledger.
    public static function record(Ingredient $ingredient, string $type, float $qty, array $extra = []): self
    {
        $ingredient->increment('stock', $qty);

        return static::create($extra + [
            'ingredient_id' => $ingredient->id,
            'type' => $type,
            'qty' => $qty,
            'user_id' => auth()->id(),
        ]);
    }
}
