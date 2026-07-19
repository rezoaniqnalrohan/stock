<?php

namespace Database\Seeders;

use App\Models\Batch;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\Shipment;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\Unit;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // --- Users (one per role) ---
        foreach ([
            ['Amara Admin', 'admin@freshstock.test', 'admin'],
            ['Wade Warehouse', 'warehouse@freshstock.test', 'warehouse_manager'],
            ['Priya Procurement', 'procurement@freshstock.test', 'procurement_officer'],
        ] as [$name, $email, $role]) {
            User::create([
                'name' => $name,
                'email' => $email,
                'role' => $role,
                'password' => Hash::make('password'),
            ]);
        }

        // --- Units ---
        $units = collect([
            ['Each', 'ea'], ['Case', 'cs'], ['Kilogram', 'kg'],
            ['Litre', 'L'], ['Dozen', 'dz'], ['Pack', 'pk'],
        ])->map(fn ($u) => Unit::create(['name' => $u[0], 'abbreviation' => $u[1]]));
        $unit = fn (string $abbr) => $units->firstWhere('abbreviation', $abbr);

        // --- Categories (with colors for the donut chart) ---
        $catData = [
            ['Beverages', '#7c3aed'],
            ['Dairy & Chilled', '#6366f1'],
            ['Frozen Foods', '#06b6d4'],
            ['Fresh Produce', '#22c55e'],
            ['Bakery', '#f59e0b'],
            ['Dry Goods', '#ef4444'],
            ['Snacks', '#ec4899'],
            ['Meat & Seafood', '#8b5cf6'],
        ];
        $categories = collect($catData)->map(fn ($c) => Category::create(['name' => $c[0], 'color' => $c[1]]));
        $cat = fn (string $name) => $categories->firstWhere('name', $name);

        // --- Warehouses ---
        $warehouses = collect([
            Warehouse::create(['name' => 'Central Distribution Center', 'code' => 'CDC', 'location' => 'Chicago, IL', 'is_cold_chain' => true]),
            Warehouse::create(['name' => 'North Cold Store', 'code' => 'NCS', 'location' => 'Minneapolis, MN', 'is_cold_chain' => true]),
            Warehouse::create(['name' => 'Southern Dry Depot', 'code' => 'SDD', 'location' => 'Dallas, TX', 'is_cold_chain' => false]),
        ]);

        // --- Suppliers ---
        $supplierNames = [
            'Harvest Fields Produce Co.', 'Blue River Dairy', 'Nordic Frozen Foods',
            'Golden Grain Distributors', 'Sunrise Beverage Group', 'Coastal Seafood Traders',
        ];
        $suppliers = collect($supplierNames)->map(fn ($n) => Supplier::create([
            'name' => $n,
            'contact_name' => fake()->name(),
            'email' => fake()->companyEmail(),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->city().', '.fake()->stateAbbr(),
        ]));

        // --- Customers ---
        $customerNames = [
            'Metro Grocers Ltd.', 'FreshMart Supermarkets', 'Corner Deli Group',
            'Bistro Supply Co.', 'GreenLeaf Cafes', 'QuickStop Convenience',
        ];
        $customers = collect($customerNames)->map(fn ($n) => Customer::create([
            'name' => $n,
            'contact_name' => fake()->name(),
            'email' => fake()->companyEmail(),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->city().', '.fake()->stateAbbr(),
        ]));

        // --- Products [name, category, unit, price, cost, storage_temp, shelf_life_days, reorder_point] ---
        $productData = [
            ['Sparkling Spring Water 12x500ml', 'Beverages', 'cs', 9.50, 5.20, 'ambient', 540, 60],
            ['Orange Juice 1L', 'Beverages', 'ea', 3.20, 1.80, 'chilled', 21, 120],
            ['Cola Soft Drink 24x330ml', 'Beverages', 'cs', 14.00, 8.10, 'ambient', 365, 40],
            ['Cold Brew Coffee 250ml', 'Beverages', 'ea', 2.90, 1.40, 'chilled', 30, 90],
            ['Whole Milk 2L', 'Dairy & Chilled', 'ea', 2.40, 1.30, 'chilled', 14, 150],
            ['Greek Yogurt 500g', 'Dairy & Chilled', 'ea', 3.60, 1.90, 'chilled', 24, 100],
            ['Mature Cheddar Block 1kg', 'Dairy & Chilled', 'kg', 11.00, 6.50, 'chilled', 90, 40],
            ['Salted Butter 250g', 'Dairy & Chilled', 'ea', 2.80, 1.50, 'chilled', 60, 80],
            ['Frozen Mixed Vegetables 1kg', 'Frozen Foods', 'ea', 3.10, 1.60, 'frozen', 365, 70],
            ['Frozen Berries 500g', 'Frozen Foods', 'ea', 4.50, 2.40, 'frozen', 365, 50],
            ['Vanilla Ice Cream 2L', 'Frozen Foods', 'ea', 6.20, 3.30, 'frozen', 300, 45],
            ['Bananas', 'Fresh Produce', 'kg', 1.30, 0.60, 'ambient', 9, 200],
            ['Roma Tomatoes', 'Fresh Produce', 'kg', 2.10, 1.00, 'chilled', 12, 120],
            ['Baby Spinach 200g', 'Fresh Produce', 'ea', 2.00, 0.95, 'chilled', 7, 90],
            ['Gala Apples', 'Fresh Produce', 'kg', 1.90, 0.85, 'chilled', 30, 140],
            ['Sourdough Loaf', 'Bakery', 'ea', 3.40, 1.55, 'ambient', 5, 60],
            ['Croissants 6pk', 'Bakery', 'pk', 4.20, 2.00, 'ambient', 4, 50],
            ['Whole Wheat Bread', 'Bakery', 'ea', 2.30, 1.05, 'ambient', 6, 80],
            ['Basmati Rice 5kg', 'Dry Goods', 'ea', 12.50, 7.20, 'ambient', 720, 30],
            ['Penne Pasta 500g', 'Dry Goods', 'ea', 1.40, 0.65, 'ambient', 730, 100],
            ['Extra Virgin Olive Oil 1L', 'Dry Goods', 'ea', 8.90, 5.10, 'ambient', 540, 40],
            ['Sea Salt 1kg', 'Dry Goods', 'ea', 1.80, 0.70, 'ambient', 1080, 60],
            ['Potato Chips 150g', 'Snacks', 'ea', 1.60, 0.75, 'ambient', 120, 130],
            ['Mixed Nuts 400g', 'Snacks', 'ea', 5.40, 3.00, 'ambient', 210, 55],
            ['Dark Chocolate Bar 100g', 'Snacks', 'ea', 2.20, 1.00, 'ambient', 300, 90],
            ['Chicken Breast Fillets 1kg', 'Meat & Seafood', 'kg', 8.40, 4.90, 'chilled', 8, 70],
            ['Atlantic Salmon Fillet 1kg', 'Meat & Seafood', 'kg', 18.50, 11.20, 'chilled', 6, 35],
            ['Pork Sausages 1kg', 'Meat & Seafood', 'kg', 6.30, 3.40, 'chilled', 10, 50],
        ];

        $products = collect();
        $i = 1;
        foreach ($productData as [$name, $catName, $unitAbbr, $price, $cost, $temp, $shelf, $reorder]) {
            $products->push(Product::create([
                'sku' => 'FS-'.str_pad((string) $i++, 4, '0', STR_PAD_LEFT),
                'name' => $name,
                'category_id' => $cat($catName)->id,
                'unit_id' => $unit($unitAbbr)->id,
                'supplier_id' => $suppliers->random()->id,
                'price' => $price,
                'cost' => $cost,
                'storage_temp' => $temp,
                'shelf_life_days' => $shelf,
                'reorder_point' => $reorder,
            ]));
        }

        // --- Batches: stock across warehouses, with expiry (some expiring soon) ---
        foreach ($products as $p) {
            $wh = $p->storage_temp === 'ambient' ? $warehouses : $warehouses->where('is_cold_chain', true);
            $batchCount = rand(1, 2);
            for ($b = 0; $b < $batchCount; $b++) {
                $qty = rand(0, max(4, (int) round($p->reorder_point * 1.8)));
                $expiry = $p->shelf_life_days > 0
                    ? now()->addDays(rand(-3, min($p->shelf_life_days, 120)))
                    : null;
                Batch::create([
                    'product_id' => $p->id,
                    'warehouse_id' => $wh->random()->id,
                    'batch_no' => 'B'.now()->format('y').'-'.str_pad((string) rand(1, 9999), 4, '0', STR_PAD_LEFT),
                    'quantity' => $qty,
                    'expiry_date' => $expiry,
                ]);
            }
        }

        // --- Stock movements: 6 months of in/out history for the charts ---
        foreach (range(5, 0) as $monthsAgo) {
            $base = now()->subMonths($monthsAgo);
            foreach (range(1, rand(12, 20)) as $n) {
                $p = $products->random();
                $isIn = fake()->boolean(55);
                StockMovement::create([
                    'product_id' => $p->id,
                    'warehouse_id' => $warehouses->random()->id,
                    'type' => $isIn ? 'in' : 'out',
                    'quantity' => $isIn ? rand(20, 200) : -rand(10, 120),
                    'reference' => $isIn ? 'GRN-'.rand(1000, 9999) : 'SO-'.rand(1000, 9999),
                    'note' => $isIn ? 'Goods received' : 'Order fulfilled',
                    'created_at' => $base->copy()->addDays(rand(0, 27)),
                    'updated_at' => $base,
                ]);
            }
        }
        // A couple of wastage adjustments (spoilage) for the wastage report
        foreach ($products->where('storage_temp', 'chilled')->take(3) as $p) {
            StockMovement::create([
                'product_id' => $p->id,
                'warehouse_id' => $warehouses->where('is_cold_chain', true)->random()->id,
                'type' => 'adjustment',
                'quantity' => -rand(5, 25),
                'reference' => 'ADJ-'.rand(1000, 9999),
                'note' => 'Spoilage / expired stock written off',
                'created_at' => now()->subDays(rand(1, 20)),
            ]);
        }

        // --- Purchase Orders ---
        $poStatuses = ['draft', 'ordered', 'received', 'ordered'];
        foreach (range(1, 6) as $n) {
            $po = PurchaseOrder::create([
                'po_number' => 'PO-'.str_pad((string) $n, 4, '0', STR_PAD_LEFT),
                'supplier_id' => $suppliers->random()->id,
                'warehouse_id' => $warehouses->random()->id,
                'status' => $poStatuses[array_rand($poStatuses)],
                'order_date' => now()->subDays(rand(2, 40)),
                'expected_date' => now()->addDays(rand(1, 10)),
                'total' => 0,
            ]);
            $total = 0;
            foreach ($products->random(rand(2, 4)) as $p) {
                $qty = rand(20, 120);
                $total += $qty * $p->cost;
                PurchaseOrderItem::create([
                    'purchase_order_id' => $po->id,
                    'product_id' => $p->id,
                    'quantity' => $qty,
                    'unit_cost' => $p->cost,
                ]);
            }
            $po->update(['total' => $total]);
        }

        // --- Sales Orders ---
        $soStatuses = ['pending', 'fulfilled', 'shipped', 'fulfilled'];
        foreach (range(1, 7) as $n) {
            $so = SalesOrder::create([
                'order_number' => 'SO-'.str_pad((string) $n, 4, '0', STR_PAD_LEFT),
                'customer_id' => $customers->random()->id,
                'warehouse_id' => $warehouses->random()->id,
                'status' => $soStatuses[array_rand($soStatuses)],
                'order_date' => now()->subDays(rand(1, 30)),
                'total' => 0,
            ]);
            $total = 0;
            foreach ($products->random(rand(2, 5)) as $p) {
                $qty = rand(5, 60);
                $total += $qty * $p->price;
                SalesOrderItem::create([
                    'sales_order_id' => $so->id,
                    'product_id' => $p->id,
                    'quantity' => $qty,
                    'unit_price' => $p->price,
                ]);
            }
            $so->update(['total' => $total]);
        }

        // --- Shipments (logistics) ---
        $types = ['inbound', 'outbound', 'transfer'];
        $shipStatuses = ['pending', 'in_transit', 'delivered'];
        foreach (range(1, 8) as $n) {
            $type = $types[array_rand($types)];
            Shipment::create([
                'reference' => 'SHP-'.str_pad((string) $n, 4, '0', STR_PAD_LEFT),
                'type' => $type,
                'origin' => $type === 'inbound' ? $suppliers->random()->name : $warehouses->random()->name,
                'destination' => $type === 'outbound' ? $customers->random()->name : $warehouses->random()->name,
                'status' => $shipStatuses[array_rand($shipStatuses)],
                'ship_date' => now()->subDays(rand(0, 15)),
            ]);
        }
    }
}
