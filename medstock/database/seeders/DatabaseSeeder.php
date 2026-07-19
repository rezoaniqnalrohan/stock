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
            ['Admin User', 'admin@medstock.test', 'admin'],
            ['Warehouse Manager', 'manager@medstock.test', 'manager'],
            ['Sales Rep', 'sales@medstock.test', 'sales'],
        ] as [$name, $email, $role]) {
            User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make('password'),
                'role' => $role,
            ]);
        }

        // --- Settings data ---
        $cats = collect(['Face Masks', 'Gloves', 'Gowns & Apparel', 'Syringes & Needles', 'Sanitizers', 'Diagnostic Kits'])
            ->mapWithKeys(fn ($n) => [$n => Category::create(['name' => $n])->id]);

        $units = collect([
            ['Box', 'BX'], ['Piece', 'PC'], ['Pair', 'PR'], ['Bottle', 'BTL'], ['Carton', 'CTN'], ['Pack', 'PK'],
        ])->mapWithKeys(fn ($u) => [$u[1] => Unit::create(['name' => $u[0], 'abbreviation' => $u[1]])->id]);

        $warehouses = collect([
            ['Central Distribution', 'Newark, NJ'],
            ['West Regional', 'Phoenix, AZ'],
        ])->map(fn ($w) => Warehouse::create(['name' => $w[0], 'location' => $w[1]]));

        $suppliers = collect([
            ['MedGuard Manufacturing', 'Laura Kim', 'sales@medguard.example', '+1 201 555 0110'],
            ['SafeHands Supplies', 'Peter Osei', 'orders@safehands.example', '+1 480 555 0132'],
            ['PureShield Labs', 'Nadia Cruz', 'contact@pureshield.example', '+1 312 555 0177'],
        ])->map(fn ($s) => Supplier::create([
            'name' => $s[0], 'contact_name' => $s[1], 'email' => $s[2], 'phone' => $s[3],
            'address' => '1 Industrial Way',
        ]));

        $customers = collect([
            ['Riverside Clinic', 'clinic'],
            ['CarePlus Pharmacy', 'pharmacy'],
            ['St. Mary Hospital', 'hospital'],
            ['Downtown Family Practice', 'clinic'],
            ['WellCare Pharmacy', 'pharmacy'],
        ])->map(fn ($c) => Customer::create([
            'name' => $c[0], 'type' => $c[1],
            'email' => strtolower(str_replace(' ', '.', $c[0])).'@example.com',
            'phone' => '+1 555 01'.rand(10, 99),
            'address' => rand(100, 999).' Main St',
        ]));

        // --- Products (SKU, name, category, unit, cost, price, reorder) ---
        $productData = [
            ['MSK-N95-001', 'N95 Respirator Mask', 'Face Masks', 'BX', 8.50, 14.00, 40],
            ['MSK-SRG-002', 'Surgical Mask 3-Ply', 'Face Masks', 'BX', 2.20, 4.50, 60],
            ['MSK-KN95-003', 'KN95 Protective Mask', 'Face Masks', 'BX', 6.00, 11.00, 35],
            ['GLV-NIT-010', 'Nitrile Exam Gloves (Blue)', 'Gloves', 'BX', 5.80, 9.90, 80],
            ['GLV-LTX-011', 'Latex Exam Gloves', 'Gloves', 'BX', 4.90, 8.50, 70],
            ['GLV-VNL-012', 'Vinyl Gloves Powder-Free', 'Gloves', 'BX', 3.40, 6.20, 55],
            ['GWN-ISO-020', 'Isolation Gown Level 2', 'Gowns & Apparel', 'PK', 12.00, 19.50, 30],
            ['GWN-SRG-021', 'Surgical Gown Sterile', 'Gowns & Apparel', 'PK', 15.50, 24.00, 25],
            ['SYR-3ML-030', 'Syringe 3ml Luer Lock', 'Syringes & Needles', 'BX', 7.10, 12.40, 45],
            ['SYR-5ML-031', 'Syringe 5ml', 'Syringes & Needles', 'BX', 8.30, 13.90, 40],
            ['NDL-21G-032', 'Hypodermic Needle 21G', 'Syringes & Needles', 'BX', 4.20, 7.80, 50],
            ['SAN-500-040', 'Hand Sanitizer Gel 500ml', 'Sanitizers', 'BTL', 2.60, 4.90, 90],
            ['SAN-5L-041', 'Surface Sanitizer 5L', 'Sanitizers', 'BTL', 9.80, 16.50, 30],
            ['DGK-COV-050', 'COVID-19 Antigen Test Kit', 'Diagnostic Kits', 'BX', 18.00, 29.00, 25],
            ['DGK-GLU-051', 'Glucose Test Strips', 'Diagnostic Kits', 'BX', 11.40, 18.00, 30],
        ];

        $products = collect($productData)->map(function ($p) use ($cats, $units, $suppliers) {
            return Product::create([
                'sku' => $p[0], 'name' => $p[1],
                'category_id' => $cats[$p[2]], 'unit_id' => $units[$p[3]],
                'supplier_id' => $suppliers->random()->id,
                'barcode' => (string) rand(1000000000000, 9999999999999),
                'cost' => $p[4], 'price' => $p[5], 'reorder_point' => $p[6],
                'description' => $p[1].' for clinical and hospital use.',
            ]);
        });

        // --- Batches: vary stock so dashboard shows in/low/out + expiring states ---
        foreach ($products as $i => $product) {
            $profile = $i % 5; // 1 low, 2 out, 3 expiring, else healthy
            foreach ($warehouses as $w) {
                $qty = match ($profile) {
                    2 => 0,
                    1 => (int) round($product->reorder_point * 0.4),
                    default => rand($product->reorder_point + 20, $product->reorder_point + 200),
                };

                $expiry = $profile === 3
                    ? now()->addDays(rand(20, 70))   // expiring soon
                    : now()->addMonths(rand(8, 24));

                if ($qty > 0) {
                    Batch::create([
                        'product_id' => $product->id,
                        'warehouse_id' => $w->id,
                        'lot_number' => 'LOT-'.strtoupper(substr(md5($product->sku.$w->id), 0, 6)),
                        'expiry_date' => $expiry,
                        'quantity' => $qty,
                    ]);
                    StockMovement::create([
                        'product_id' => $product->id, 'warehouse_id' => $w->id,
                        'type' => 'in', 'quantity' => $qty, 'reference' => 'Opening',
                        'note' => 'Initial stock', 'user_id' => 1,
                        'created_at' => now()->subDays(rand(30, 90)),
                    ]);
                }
            }
        }

        // --- Historical movements for the dashboard chart (last 30 days) ---
        for ($d = 30; $d >= 0; $d--) {
            $day = now()->subDays($d);
            foreach (range(1, rand(2, 6)) as $m) {
                $product = $products->random();
                $w = $warehouses->random();
                $out = rand(0, 1) === 1;
                StockMovement::create([
                    'product_id' => $product->id, 'warehouse_id' => $w->id,
                    'type' => $out ? 'out' : 'in',
                    'quantity' => $out ? -rand(5, 40) : rand(10, 60),
                    'reference' => $out ? 'SO' : 'PO',
                    'note' => $out ? 'Sales shipment' : 'Purchase receipt',
                    'user_id' => rand(1, 3),
                    'created_at' => $day->copy()->addHours(rand(8, 17)),
                ]);
            }
        }

        // --- Purchase Orders ---
        foreach (range(1, 4) as $n) {
            $po = PurchaseOrder::create([
                'supplier_id' => $suppliers->random()->id,
                'warehouse_id' => $warehouses->random()->id,
                'status' => ['received', 'ordered', 'draft'][rand(0, 2)],
                'order_date' => now()->subDays(rand(3, 40)),
                'user_id' => 2,
            ]);
            $total = 0;
            foreach ($products->random(rand(2, 4)) as $product) {
                $qty = rand(20, 120);
                PurchaseOrderItem::create([
                    'purchase_order_id' => $po->id, 'product_id' => $product->id,
                    'quantity' => $qty, 'cost' => $product->cost,
                    'lot_number' => 'LOT-'.strtoupper(substr(md5($po->id.$product->id), 0, 6)),
                    'expiry_date' => now()->addMonths(rand(10, 20)),
                ]);
                $total += $qty * $product->cost;
            }
            $po->update(['total' => $total]);
        }

        // --- Sales Orders ---
        foreach (range(1, 6) as $n) {
            $so = SalesOrder::create([
                'customer_id' => $customers->random()->id,
                'warehouse_id' => $warehouses->random()->id,
                'status' => ['shipped', 'packed', 'picked', 'pending'][rand(0, 3)],
                'order_date' => now()->subDays(rand(0, 30)),
                'user_id' => 3,
            ]);
            $total = 0;
            foreach ($products->random(rand(2, 5)) as $product) {
                $qty = rand(5, 40);
                SalesOrderItem::create([
                    'sales_order_id' => $so->id, 'product_id' => $product->id,
                    'quantity' => $qty, 'price' => $product->price,
                ]);
                $total += $qty * $product->price;
            }
            $so->update(['total' => $total]);
        }
    }
}
