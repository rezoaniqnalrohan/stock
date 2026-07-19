<?php

namespace Tests\Feature;

use App\Models\Ingredient;
use App\Models\MenuItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SmokeTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/')->assertRedirect('/login');
    }

    public function test_dashboard_loads_for_admin(): void
    {
        $this->actingAs(User::where('role', 'admin')->first())
            ->get('/')->assertOk()->assertSee('Stock value');
    }

    public function test_sale_deducts_recipe_ingredients(): void
    {
        $burger = MenuItem::where('name', 'Beef Burger')->firstOrFail();
        $patty = Ingredient::where('name', 'Beef Patty')->firstOrFail();
        $before = $patty->stock;

        $this->actingAs(User::where('role', 'admin')->first())
            ->post('/sales', ['items' => [$burger->id => 2]])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertEquals($before - 2, $patty->fresh()->stock);
        $this->assertDatabaseHas('stock_movements', ['ingredient_id' => $patty->id, 'type' => 'sale']);
    }

    public function test_staff_cannot_manage_team(): void
    {
        $this->actingAs(User::where('role', 'staff')->first())->get('/users')->assertForbidden();
    }
}
