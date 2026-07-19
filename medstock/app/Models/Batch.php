<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    protected $fillable = ['product_id', 'warehouse_id', 'lot_number', 'expiry_date', 'quantity'];

    protected $casts = ['expiry_date' => 'date'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    // Single place that changes stock + logs a movement. FEFO on decrease.
    // For positive delta with a lot/expiry, a matching batch is created (PO receiving).
    public static function move(
        int $productId, int $warehouseId, int $delta, string $type, string $reference,
        ?string $note = null, ?string $lot = null, $expiry = null
    ): void {
        if ($delta >= 0) {
            $key = ['product_id' => $productId, 'warehouse_id' => $warehouseId, 'lot_number' => $lot ?? 'ADJ'];
            $batch = static::firstOrCreate($key, ['quantity' => 0, 'expiry_date' => $expiry ?? now()->addYear()]);
            $batch->increment('quantity', $delta);
        } else {
            $remaining = -$delta;
            $batches = static::where('product_id', $productId)->where('warehouse_id', $warehouseId)
                ->where('quantity', '>', 0)->orderByRaw('expiry_date is null, expiry_date asc')->get();
            foreach ($batches as $batch) {
                if ($remaining <= 0) break;
                $take = min($batch->quantity, $remaining);
                $batch->decrement('quantity', $take);
                $remaining -= $take;
            }
        }

        StockMovement::create([
            'product_id' => $productId, 'warehouse_id' => $warehouseId,
            'type' => $type, 'quantity' => $delta, 'reference' => $reference,
            'note' => $note, 'user_id' => \Illuminate\Support\Facades\Auth::id(),
        ]);
    }
}
