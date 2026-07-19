<?php

namespace Database\Seeders;

use App\Models\Ingredient;
use App\Models\MenuItem;
use App\Models\Sale;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create(['name' => 'Jaed Talha', 'email' => 'admin@faststock.test', 'password' => 'password', 'role' => 'admin']);
        $staff = User::create(['name' => 'Rakib Hasan', 'email' => 'staff@faststock.test', 'password' => 'password', 'role' => 'staff']);
        $users = collect([$admin, $staff]);

        $suppliers = collect([
            ['name' => 'Dhaka Fresh Meat Co.', 'phone' => '01711-000001'],
            ['name' => 'Green Valley Produce', 'phone' => '01822-000002'],
            ['name' => 'Metro Beverage Distributors', 'phone' => '01933-000003'],
            ['name' => 'City Bakery Supplies', 'phone' => '01644-000004'],
            ['name' => 'Karim Packaging House', 'phone' => '01555-000005'],
        ])->map(fn ($s) => Supplier::create($s));

        // name, category, unit, cost (BDT), reorder level, restock target, shelf life in days (null = non-perishable)
        $rows = [
            ['Beef Patty', 'Meat', 'pcs', 85, 40, 150, 6],
            ['Chicken Breast', 'Meat', 'kg', 320, 10, 30, 5],
            ['Chicken Drumstick', 'Meat', 'pcs', 55, 30, 90, 5],
            ['Burger Bun', 'Bakery', 'pcs', 12, 60, 220, 4],
            ['Hot Dog Bun', 'Bakery', 'pcs', 10, 40, 120, 4],
            ['Cheese Slice', 'Dairy', 'pcs', 18, 80, 280, 20],
            ['Butter', 'Dairy', 'kg', 650, 3, 8, 30],
            ['Ice Cream Mix', 'Dairy', 'L', 180, 4, 12, 10],
            ['Lettuce', 'Produce', 'kg', 90, 4, 12, 4],
            ['Tomato', 'Produce', 'kg', 60, 5, 15, 6],
            ['Onion', 'Produce', 'kg', 45, 5, 20, 15],
            ['Potato', 'Produce', 'kg', 35, 25, 80, 20],
            ['Cooking Oil', 'Pantry', 'L', 165, 15, 40, null],
            ['Flour', 'Pantry', 'kg', 55, 10, 30, null],
            ['Breading Mix', 'Pantry', 'kg', 120, 6, 15, null],
            ['Mayonnaise', 'Sauces', 'kg', 240, 4, 10, 25],
            ['Ketchup', 'Sauces', 'kg', 140, 5, 12, 40],
            ['BBQ Sauce', 'Sauces', 'kg', 260, 2, 6, 40],
            ['Cola Syrup', 'Beverages', 'L', 210, 5, 15, null],
            ['Burger Box', 'Packaging', 'pcs', 8, 100, 350, null],
            ['Paper Cup 350ml', 'Packaging', 'pcs', 4, 150, 450, null],
            ['French Fry Bag', 'Packaging', 'pcs', 2.5, 150, 450, null],
        ];
        $targets = $shelfLife = [];
        $ingredients = collect($rows)->mapWithKeys(function ($r) use (&$targets, &$shelfLife) {
            $ingredient = Ingredient::create(['name' => $r[0], 'category' => $r[1], 'unit' => $r[2], 'cost' => $r[3], 'reorder_level' => $r[4]]);
            $targets[$ingredient->id] = $r[5];
            $shelfLife[$ingredient->id] = $r[6];

            return [$r[0] => $ingredient];
        });

        $menu = [
            ['Beef Burger', 220, ['Beef Patty' => 1, 'Burger Bun' => 1, 'Cheese Slice' => 1, 'Lettuce' => 0.02, 'Tomato' => 0.03, 'Onion' => 0.02, 'Mayonnaise' => 0.015, 'Ketchup' => 0.01, 'Burger Box' => 1]],
            ['Double Beef Burger', 350, ['Beef Patty' => 2, 'Burger Bun' => 1, 'Cheese Slice' => 2, 'Lettuce' => 0.02, 'Onion' => 0.02, 'BBQ Sauce' => 0.02, 'Burger Box' => 1]],
            ['Chicken Burger', 180, ['Chicken Breast' => 0.12, 'Breading Mix' => 0.03, 'Cooking Oil' => 0.02, 'Burger Bun' => 1, 'Lettuce' => 0.02, 'Mayonnaise' => 0.015, 'Burger Box' => 1]],
            ['Fried Chicken (2 pc)', 200, ['Chicken Drumstick' => 2, 'Breading Mix' => 0.05, 'Flour' => 0.03, 'Cooking Oil' => 0.05, 'Burger Box' => 1]],
            ['French Fries', 90, ['Potato' => 0.18, 'Cooking Oil' => 0.03, 'French Fry Bag' => 1]],
            ['Chicken Sandwich', 160, ['Chicken Breast' => 0.1, 'Hot Dog Bun' => 1, 'Lettuce' => 0.015, 'Mayonnaise' => 0.01]],
            ['Soft Drink', 60, ['Cola Syrup' => 0.05, 'Paper Cup 350ml' => 1]],
            ['Vanilla Soft Serve', 80, ['Ice Cream Mix' => 0.1, 'Paper Cup 350ml' => 1]],
        ];
        $menuItems = collect($menu)->map(function ($m) use ($ingredients) {
            $item = MenuItem::create(['name' => $m[0], 'price' => $m[1]]);
            $item->ingredients()->attach(collect($m[2])->mapWithKeys(fn ($qty, $name) => [$ingredients[$name]->id => ['qty' => $qty]]));

            return $item->load('ingredients');
        });

        $restock = function (Ingredient $ingredient) use (&$targets, &$shelfLife, $suppliers, $admin) {
            StockMovement::record($ingredient, 'purchase', $targets[$ingredient->id], [
                'supplier_id' => $suppliers->random()->id,
                'unit_cost' => $ingredient->cost,
                'expiry_date' => $shelfLife[$ingredient->id] ? today()->addDays($shelfLife[$ingredient->id]) : null,
                'user_id' => $admin->id,
            ]);
        };

        // Two weeks of simulated trading history.
        $base = Carbon::now(); // anchor to the real clock; now() is frozen once setTestNow runs
        foreach (range(13, 0) as $daysAgo) {
            Carbon::setTestNow($base->copy()->startOfDay()->subDays($daysAgo)->addHours(8));
            if ($daysAgo === 13) {
                $ingredients->each($restock); // opening stock
            }

            foreach (range(1, rand(10, 22)) as $n) {
                Carbon::setTestNow(today()->addMinutes(rand(10 * 60, 22 * 60)));
                $order = $menuItems->random(rand(1, 3))->mapWithKeys(fn ($m) => [$m->id => rand(1, 3)]);

                $needed = [];
                foreach ($order as $id => $qty) {
                    foreach ($menuItems->firstWhere('id', $id)->ingredients as $ing) {
                        $needed[$ing->id] = ($needed[$ing->id] ?? 0) + $ing->pivot->qty * $qty;
                    }
                }
                foreach ($needed as $ingredientId => $qty) {
                    $fresh = Ingredient::find($ingredientId);
                    if ($fresh->stock < $qty) {
                        $restock($fresh);
                    }
                }

                $seller = $users->random();
                $sale = Sale::create([
                    'user_id' => $seller->id,
                    'total' => $order->reduce(fn ($sum, $qty, $id) => $sum + $menuItems->firstWhere('id', $id)->price * $qty, 0),
                ]);
                foreach ($order as $id => $qty) {
                    $sale->items()->create(['menu_item_id' => $id, 'qty' => $qty, 'price' => $menuItems->firstWhere('id', $id)->price]);
                }
                foreach ($needed as $ingredientId => $qty) {
                    StockMovement::record(Ingredient::find($ingredientId), 'sale', -$qty, ['note' => "Sale #$sale->id", 'user_id' => $seller->id]);
                }
            }

            if ($daysAgo % 3 === 0) {
                $fresh = $ingredients->only(['Lettuce', 'Tomato', 'Burger Bun'])->random()->fresh();
                $qty = min((float) $fresh->stock, $fresh->unit === 'pcs' ? rand(2, 8) : rand(3, 12) / 10);
                if ($qty > 0) {
                    StockMovement::record($fresh, 'waste', -$qty, [
                        'note' => collect(['end-of-day expiry', 'dropped during prep', 'burnt batch', 'spoiled in chiller'])->random(),
                        'user_id' => $users->random()->id,
                    ]);
                }
            }
        }
        Carbon::setTestNow();

        // Batches that exercise the expiring-soon warning.
        foreach ([['Lettuce', 3, 2], ['Ice Cream Mix', 4, 4], ['Beef Patty', 20, 5]] as [$name, $qty, $days]) {
            StockMovement::record($ingredients[$name], 'purchase', $qty, [
                'supplier_id' => $suppliers->random()->id,
                'unit_cost' => $ingredients[$name]->cost,
                'expiry_date' => today()->addDays($days),
                'user_id' => $admin->id,
            ]);
        }
    }
}
