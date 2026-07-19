<?php

namespace Tests\Feature;

use App\Models\Batch;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\SalesOrder;
use App\Models\User;
use App\Models\Warehouse;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

// ponytail: one smoke test — proves the app boots, auth guards, every nav page renders,
// and the stock paths that actually move numbers (adjust, transfer, receive, fulfil) work.
class SmokeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    private function admin(): User
    {
        return User::where('email', 'admin@freshstock.test')->first();
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
        $this->get('/login')->assertOk();
    }

    public function test_demo_users_can_sign_in(): void
    {
        $this->post('/login', ['email' => 'admin@freshstock.test', 'password' => 'password'])
            ->assertRedirect('/dashboard');
        $this->assertAuthenticated();
    }

    public function test_every_nav_page_renders(): void
    {
        $this->actingAs($this->admin());

        foreach ([
            '/dashboard', '/products', '/products/create',
            '/inventory', '/inventory/adjust', '/inventory/transfer', '/inventory/expiring',
            '/suppliers', '/suppliers/create', '/purchase-orders', '/purchase-orders/create',
            '/customers', '/customers/create', '/orders', '/orders/create',
            '/shipments', '/shipments/create',
            '/reports/valuation', '/reports/movement', '/reports/expiring', '/reports/wastage',
            '/settings',
        ] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_detail_pages_render(): void
    {
        $this->actingAs($this->admin());

        $this->get('/products/'.Product::first()->id)->assertOk();
        $this->get('/orders/'.SalesOrder::first()->id)->assertOk();
        $this->get('/purchase-orders/'.PurchaseOrder::first()->id)->assertOk();
    }

    public function test_product_can_be_created(): void
    {
        $this->actingAs($this->admin());

        $this->post('/products', [
            'sku' => 'TEST-001', 'name' => 'Test Oat Milk',
            'category_id' => \App\Models\Category::first()->id,
            'unit_id' => \App\Models\Unit::first()->id,
            'price' => 3.20, 'cost' => 1.80,
            'storage_temp' => 'chilled', 'shelf_life_days' => 14, 'reorder_point' => 24,
        ])->assertRedirect('/products');

        $this->assertDatabaseHas('products', ['sku' => 'TEST-001']);
    }

    public function test_stock_adjustment_changes_quantity_and_logs_movement(): void
    {
        $this->actingAs($this->admin());
        $product = Product::first();
        $before = $product->stockOnHand();

        $this->post('/inventory/adjust', [
            'product_id' => $product->id,
            'warehouse_id' => Warehouse::first()->id,
            'quantity' => 25,
            'note' => 'Smoke test',
        ])->assertRedirect('/inventory');

        $this->assertSame($before + 25, $product->fresh()->stockOnHand());
        $this->assertDatabaseHas('stock_movements', ['product_id' => $product->id, 'type' => 'adjustment', 'quantity' => 25]);
    }

    public function test_transfer_moves_stock_between_warehouses(): void
    {
        $this->actingAs($this->admin());
        [$from, $to] = Warehouse::take(2)->get()->all();
        $product = Product::whereHas('batches', fn ($q) => $q->where('warehouse_id', $from->id)->where('quantity', '>', 50))->first();

        $fromBefore = (int) Batch::where('product_id', $product->id)->where('warehouse_id', $from->id)->sum('quantity');
        $toBefore = (int) Batch::where('product_id', $product->id)->where('warehouse_id', $to->id)->sum('quantity');

        $this->post('/inventory/transfer', [
            'product_id' => $product->id,
            'from_warehouse_id' => $from->id,
            'to_warehouse_id' => $to->id,
            'quantity' => 10,
        ])->assertRedirect();

        $this->assertSame($fromBefore - 10, (int) Batch::where('product_id', $product->id)->where('warehouse_id', $from->id)->sum('quantity'));
        $this->assertSame($toBefore + 10, (int) Batch::where('product_id', $product->id)->where('warehouse_id', $to->id)->sum('quantity'));
    }

    public function test_receiving_a_purchase_order_adds_stock(): void
    {
        $this->actingAs($this->admin());
        $po = PurchaseOrder::where('status', '!=', 'received')->with('items')->first()
            ?? PurchaseOrder::with('items')->first();
        $po->update(['status' => 'ordered']);

        $item = $po->items->first();
        $before = $item->product->stockOnHand();

        $this->post("/purchase-orders/{$po->id}/receive")->assertRedirect();

        $this->assertSame('received', $po->fresh()->status);
        $this->assertSame($before + $item->quantity, $item->product->fresh()->stockOnHand());
    }

    public function test_fulfilling_a_sales_order_decrements_stock(): void
    {
        $this->actingAs($this->admin());

        $order = SalesOrder::with('items')->first();
        $order->update(['status' => 'pending']);

        // Guarantee enough stock in the order's warehouse so the fulfil path runs to completion.
        foreach ($order->items as $item) {
            Batch::create([
                'product_id' => $item->product_id,
                'warehouse_id' => $order->warehouse_id,
                'batch_no' => 'SMOKE-'.$item->product_id,
                'quantity' => $item->quantity + 100,
                'expiry_date' => now()->addYear(),
            ]);
        }

        $item = $order->items->first();
        $before = $item->product->fresh()->stockOnHand();

        $this->post("/orders/{$order->id}/fulfill")->assertRedirect();

        $this->assertSame('fulfilled', $order->fresh()->status);
        $this->assertSame($before - $item->quantity, $item->product->fresh()->stockOnHand());
    }
}
