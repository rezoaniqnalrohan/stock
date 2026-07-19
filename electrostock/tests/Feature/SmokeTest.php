<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Sale;
use App\Models\Stock;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SmokeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/')->assertRedirect('/login');
    }

    public function test_key_pages_render_for_authenticated_user(): void
    {
        $user = User::where('role', 'admin')->first();

        foreach (['/', '/sell', '/products', '/inventory', '/transfers', '/purchase-orders', '/suppliers', '/customers', '/reports', '/setup'] as $path) {
            $this->actingAs($user)->get($path)->assertOk();
        }
    }

    public function test_form_pages_render(): void
    {
        $user = User::where('role', 'admin')->first();

        foreach (['/products/create', '/purchase-orders/create', '/products/'.Product::first()->id.'/edit'] as $path) {
            $this->actingAs($user)->get($path)->assertOk();
        }
    }

    public function test_pos_records_a_sale_and_decrements_stock(): void
    {
        $user = User::where('role', 'admin')->first();
        $product = Product::first();
        $before = Stock::where(['product_id' => $product->id, 'outlet_id' => 1])->value('quantity');

        $this->actingAs($user)->post('/sell', [
            'outlet_id' => 1,
            'items' => [['product_id' => $product->id, 'quantity' => 2]],
        ])->assertRedirect('/sell');

        $this->assertDatabaseCount('sales', Sale::count());
        $this->assertEquals($before - 2, Stock::where(['product_id' => $product->id, 'outlet_id' => 1])->value('quantity'));
    }

    public function test_cashier_cannot_reach_setup(): void
    {
        $cashier = User::where('role', 'cashier')->first();
        $this->actingAs($cashier)->get('/setup')->assertForbidden();
    }
}
