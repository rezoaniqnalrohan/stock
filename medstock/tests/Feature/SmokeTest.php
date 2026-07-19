<?php

namespace Tests\Feature;

use App\Models\Batch;
use App\Models\Product;
use App\Models\SalesOrder;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

// ponytail: one smoke test — proves the app boots, auth guards, every nav page renders,
// and the two stock paths that actually move numbers (receive + ship) work.
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
        return User::where('email', 'admin@medstock.test')->first();
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/')->assertRedirect('/login');
        $this->get('/login')->assertOk();
    }

    public function test_demo_users_can_sign_in(): void
    {
        $this->post('/login', ['email' => 'admin@medstock.test', 'password' => 'password'])
            ->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
    }

    public function test_every_nav_page_renders(): void
    {
        $this->actingAs($this->admin());

        foreach ([
            '/', '/products', '/products/create', '/inventory',
            '/suppliers', '/suppliers/create', '/purchase-orders', '/purchase-orders/create',
            '/customers', '/customers/create', '/sales-orders', '/sales-orders/create',
            '/reports/valuation', '/reports/movement', '/reports/expiring', '/reports/sales',
            '/settings',
        ] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_detail_pages_render(): void
    {
        $this->actingAs($this->admin());

        $this->get('/products/'.Product::first()->id)->assertOk();
        $this->get('/sales-orders/'.SalesOrder::first()->id)->assertOk();
    }

    public function test_product_can_be_created(): void
    {
        $this->actingAs($this->admin());

        $this->post('/products', [
            'sku' => 'TEST-001', 'name' => 'Test Face Shield',
            'category_id' => \App\Models\Category::first()->id,
            'unit_id' => \App\Models\Unit::first()->id,
            'cost' => 3.50, 'price' => 6.00, 'reorder_point' => 10,
        ])->assertRedirect('/products');

        $this->assertDatabaseHas('products', ['sku' => 'TEST-001']);
    }

    public function test_stock_adjustment_changes_quantity_and_logs_movement(): void
    {
        $this->actingAs($this->admin());
        $product = Product::first();
        $before = $product->fresh()->stock;

        $this->post('/inventory/adjust', [
            'product_id' => $product->id,
            'warehouse_id' => \App\Models\Warehouse::first()->id,
            'quantity' => 25,
            'note' => 'Smoke test',
        ])->assertRedirect();

        $this->assertSame($before + 25, $product->fresh()->stock);
        $this->assertDatabaseHas('stock_movements', ['product_id' => $product->id, 'type' => 'adjust', 'quantity' => 25]);
    }

    public function test_transfer_moves_stock_between_warehouses(): void
    {
        $this->actingAs($this->admin());
        [$from, $to] = \App\Models\Warehouse::take(2)->get()->all();
        $product = Product::whereHas('batches', fn ($q) => $q->where('warehouse_id', $from->id)->where('quantity', '>', 50))->first();

        $fromBefore = Batch::where('product_id', $product->id)->where('warehouse_id', $from->id)->sum('quantity');
        $toBefore = Batch::where('product_id', $product->id)->where('warehouse_id', $to->id)->sum('quantity');

        $this->post('/inventory/transfer', [
            'product_id' => $product->id,
            'from_warehouse_id' => $from->id,
            'to_warehouse_id' => $to->id,
            'quantity' => 10,
        ])->assertRedirect();

        $this->assertSame((int) $fromBefore - 10, (int) Batch::where('product_id', $product->id)->where('warehouse_id', $from->id)->sum('quantity'));
        $this->assertSame((int) $toBefore + 10, (int) Batch::where('product_id', $product->id)->where('warehouse_id', $to->id)->sum('quantity'));
    }

    public function test_receiving_a_purchase_order_adds_stock(): void
    {
        $this->actingAs($this->admin());
        $po = \App\Models\PurchaseOrder::where('status', '!=', 'received')->with('items')->first()
            ?? \App\Models\PurchaseOrder::with('items')->first();
        $po->update(['status' => 'ordered']);

        $item = $po->items->first();
        $before = $item->product->fresh()->stock;

        $this->post("/purchase-orders/{$po->id}/receive")->assertRedirect();

        $this->assertSame('received', $po->fresh()->status);
        $this->assertSame($before + $item->quantity, $item->product->fresh()->stock);
    }

    public function test_shipping_a_sales_order_decrements_stock(): void
    {
        $this->actingAs($this->admin());

        $order = SalesOrder::where('status', 'packed')->with('items')->first();
        if (! $order) {
            $order = SalesOrder::with('items')->first();
            $order->update(['status' => 'packed']);
        }

        // Guarantee enough stock so the ship path runs.
        foreach ($order->items as $item) {
            Batch::create([
                'product_id' => $item->product_id, 'warehouse_id' => $order->warehouse_id,
                'lot_number' => 'SMOKE', 'expiry_date' => now()->addYear(), 'quantity' => $item->quantity + 100,
            ]);
        }

        $item = $order->items->first();
        $before = $item->product->fresh()->stock;

        $this->post("/sales-orders/{$order->id}/advance")->assertRedirect();

        $this->assertSame('shipped', $order->fresh()->status);
        $this->assertSame($before - $item->quantity, $item->product->fresh()->stock);
    }
}
