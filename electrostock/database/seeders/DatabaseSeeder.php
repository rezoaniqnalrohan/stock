<?php

namespace Database\Seeders;

use App\Models\Adjustment;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Outlet;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Stock;
use App\Models\Supplier;
use App\Models\Transfer;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // --- Outlets ---
        $outlets = collect([
            ['name' => 'Downtown Flagship', 'code' => 'DTN', 'location' => 'Austin, TX', 'is_warehouse' => false],
            ['name' => 'Northgate Mall', 'code' => 'NGT', 'location' => 'Dallas, TX', 'is_warehouse' => false],
            ['name' => 'Westside Store', 'code' => 'WST', 'location' => 'Houston, TX', 'is_warehouse' => false],
            ['name' => 'Central Warehouse', 'code' => 'WH1', 'location' => 'San Antonio, TX', 'is_warehouse' => true],
        ])->map(fn ($o) => Outlet::create($o));

        // --- Users (one per role) ---
        User::create(['name' => 'Sasha Merkel', 'email' => 'admin@electrostock.test', 'role' => 'admin', 'outlet_id' => $outlets[0]->id, 'password' => Hash::make('password')]);
        User::create(['name' => 'Marcus Lee', 'email' => 'manager@electrostock.test', 'role' => 'manager', 'outlet_id' => $outlets[1]->id, 'password' => Hash::make('password')]);
        User::create(['name' => 'Priya Nair', 'email' => 'cashier@electrostock.test', 'role' => 'cashier', 'outlet_id' => $outlets[0]->id, 'password' => Hash::make('password')]);

        // --- Categories & Brands ---
        $categories = collect(['Smartphones', 'Laptops', 'Headphones', 'Wearables', 'Accessories'])
            ->mapWithKeys(fn ($n) => [$n => Category::create(['name' => $n])]);
        $brands = collect(['Apple', 'Samsung', 'Sony', 'Bose', 'Dell', 'Anker', 'Beats'])
            ->mapWithKeys(fn ($n) => [$n => Brand::create(['name' => $n])]);

        // --- Products (electronics with variants) ---
        // [name, brand, category, price, cost, variant, reorder, thumb]
        $catalog = [
            ['iPhone 15 Pro', 'Apple', 'Smartphones', 1199, 890, 'Titanium / 256GB', 6, '📱'],
            ['iPhone 15', 'Apple', 'Smartphones', 899, 660, 'Blue / 128GB', 6, '📱'],
            ['Galaxy S24 Ultra', 'Samsung', 'Smartphones', 1299, 940, 'Black / 512GB', 5, '📱'],
            ['Galaxy A55', 'Samsung', 'Smartphones', 449, 320, 'Navy / 128GB', 8, '📱'],
            ['MacBook Air M3', 'Apple', 'Laptops', 1299, 980, 'Midnight / 16GB', 4, '💻'],
            ['MacBook Pro 14"', 'Apple', 'Laptops', 1999, 1520, 'Space Gray / 512GB', 3, '💻'],
            ['Dell XPS 13', 'Dell', 'Laptops', 1099, 820, 'Silver / 16GB', 4, '💻'],
            ['Sony WH-1000XM5', 'Sony', 'Headphones', 399, 250, 'Black', 7, '🎧'],
            ['Bose QuietComfort Ultra', 'Bose', 'Headphones', 429, 270, 'White Smoke', 6, '🎧'],
            ['Beats Studio Pro', 'Beats', 'Headphones', 349, 210, 'Deep Brown', 8, '🎧'],
            ['AirPods Pro 2', 'Apple', 'Headphones', 249, 150, 'USB-C', 12, '🎧'],
            ['Apple Watch Series 9', 'Apple', 'Wearables', 429, 300, 'Midnight / 45mm', 6, '⌚'],
            ['Galaxy Watch 6', 'Samsung', 'Wearables', 329, 220, 'Graphite / 44mm', 6, '⌚'],
            ['Anker 737 Power Bank', 'Anker', 'Accessories', 149, 85, '24000mAh', 15, '🔋'],
            ['Anker USB-C Charger', 'Anker', 'Accessories', 59, 28, '65W', 20, '🔌'],
            ['Apple MagSafe Charger', 'Apple', 'Accessories', 39, 19, '15W', 25, '🔌'],
        ];

        $products = collect($catalog)->map(fn ($row, $i) => Product::create([
            'sku' => 'ELS-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
            'name' => $row[0],
            'brand_id' => $brands[$row[1]]->id,
            'category_id' => $categories[$row[2]]->id,
            'variant' => $row[5],
            'price' => $row[3],
            'cost' => $row[4],
            'barcode' => (string) fake()->ean13(),
            'image_url' => $row[7], // emoji thumbnail
            'reorder_point' => $row[6],
        ]));

        // --- Stock per outlet ---
        foreach ($products as $p) {
            foreach ($outlets as $o) {
                $qty = $o->is_warehouse ? rand(20, 80) : rand(0, 25);
                if (! $o->is_warehouse && rand(1, 5) === 1) {
                    $qty = rand(0, $p->reorder_point); // guarantee some low-stock cases
                }
                Stock::create(['product_id' => $p->id, 'outlet_id' => $o->id, 'quantity' => $qty]);
            }
        }

        // --- Suppliers ---
        $suppliers = collect([
            'TechDist Global', 'Pacific Electronics Supply', 'Nova Wholesale', 'BrightLine Distribution',
        ])->map(fn ($n) => Supplier::create([
            'name' => $n,
            'contact' => fake()->name(),
            'email' => fake()->companyEmail(),
            'phone' => fake()->phoneNumber(),
        ]));

        // --- Customers ---
        $customers = collect(range(1, 12))->map(fn () => Customer::create([
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'phone' => fake()->phoneNumber(),
        ]));

        // --- Sales across the last 30 days (drives dashboard charts) ---
        $retail = $outlets->where('is_warehouse', false)->values();
        $ref = 1;
        for ($day = 30; $day >= 0; $day--) {
            $date = now()->subDays($day);
            for ($s = 0, $salesToday = rand(2, 7); $s < $salesToday; $s++) {
                $outlet = $retail->random();
                $chosen = $products->random(rand(1, 4));
                $total = 0;
                $itemsCount = 0;
                $sale = Sale::create([
                    'reference' => 'S-' . str_pad($ref++, 5, '0', STR_PAD_LEFT),
                    'outlet_id' => $outlet->id,
                    'user_id' => 3,
                    'customer_id' => rand(1, 3) === 1 ? $customers->random()->id : null,
                    'created_at' => $date,
                    'updated_at' => $date,
                ]);
                foreach ($chosen as $prod) {
                    $qty = rand(1, 2);
                    SaleItem::create([
                        'sale_id' => $sale->id,
                        'product_id' => $prod->id,
                        'quantity' => $qty,
                        'price' => $prod->price,
                        'created_at' => $date,
                        'updated_at' => $date,
                    ]);
                    $total += $qty * $prod->price;
                    $itemsCount += $qty;
                }
                $sale->update(['total' => $total, 'items_count' => $itemsCount]);
                if ($sale->customer_id) {
                    Customer::whereKey($sale->customer_id)->increment('total_spent', $total);
                }
            }
        }

        // --- Transfers (some pending, some received) ---
        $warehouse = $outlets->firstWhere('is_warehouse', true);
        foreach (range(1, 5) as $i) {
            Transfer::create([
                'from_outlet_id' => $warehouse->id,
                'to_outlet_id' => $retail->random()->id,
                'product_id' => $products->random()->id,
                'quantity' => rand(5, 20),
                'status' => $i <= 2 ? 'pending' : 'received',
                'received_at' => $i <= 2 ? null : now()->subDays(rand(1, 10)),
            ]);
        }

        // --- Purchase Orders (dispatched + received) ---
        foreach (range(1, 6) as $i) {
            $po = PurchaseOrder::create([
                'reference' => 'PO-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'supplier_id' => $suppliers->random()->id,
                'outlet_id' => $warehouse->id,
                'status' => $i <= 4 ? 'dispatched' : 'received',
                'received_at' => $i <= 4 ? null : now()->subDays(rand(1, 8)),
            ]);
            $total = 0;
            foreach ($products->random(rand(2, 4)) as $prod) {
                $qty = rand(10, 40);
                PurchaseOrderItem::create([
                    'purchase_order_id' => $po->id,
                    'product_id' => $prod->id,
                    'quantity' => $qty,
                    'cost' => $prod->cost,
                ]);
                $total += $qty * $prod->cost;
            }
            $po->update(['total' => $total]);
        }

        // --- Stock adjustments (audit trail) ---
        $reasons = ['Damaged unit', 'Stock count correction', 'Returned item', 'Found stock'];
        foreach (range(1, 4) as $i) {
            Adjustment::create([
                'product_id' => $products->random()->id,
                'outlet_id' => $retail->random()->id,
                'user_id' => 1,
                'delta' => [-2, -1, 3, 5][array_rand([-2, -1, 3, 5])],
                'reason' => $reasons[array_rand($reasons)],
            ]);
        }
    }
}
