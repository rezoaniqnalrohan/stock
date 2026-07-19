<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('outlets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('location')->nullable();
            $table->boolean('is_warehouse')->default(false);
            $table->timestamps();
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('sku')->unique();
            $table->string('name');
            $table->foreignId('brand_id')->constrained();
            $table->foreignId('category_id')->constrained();
            $table->string('variant')->nullable();       // e.g. "Black / 256GB"
            $table->decimal('price', 12, 2)->default(0);
            $table->decimal('cost', 12, 2)->default(0);
            $table->string('barcode')->nullable();
            $table->string('image_url')->nullable();
            $table->unsignedInteger('reorder_point')->default(5);
            $table->timestamps();
        });

        // Per-outlet stock levels
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('outlet_id')->constrained()->cascadeOnDelete();
            $table->integer('quantity')->default(0);
            $table->timestamps();
            $table->unique(['product_id', 'outlet_id']);
        });

        // Manual stock adjustments (audit trail)
        Schema::create('adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained();
            $table->foreignId('outlet_id')->constrained();
            $table->foreignId('user_id')->nullable()->constrained();
            $table->integer('delta');               // +/- change applied
            $table->string('reason')->nullable();
            $table->timestamps();
        });

        // One-line stock transfer between outlets
        Schema::create('transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_outlet_id')->constrained('outlets');
            $table->foreignId('to_outlet_id')->constrained('outlets');
            $table->foreignId('product_id')->constrained();
            $table->integer('quantity');
            $table->string('status')->default('pending'); // pending | received
            $table->timestamp('received_at')->nullable();
            $table->timestamps();
        });

        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('contact')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->timestamps();
        });

        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('supplier_id')->constrained();
            $table->foreignId('outlet_id')->constrained();
            $table->string('status')->default('draft'); // draft | dispatched | received
            $table->decimal('total', 12, 2)->default(0);
            $table->timestamp('received_at')->nullable();
            $table->timestamps();
        });

        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained();
            $table->integer('quantity');
            $table->decimal('cost', 12, 2);
            $table->timestamps();
        });

        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->decimal('total_spent', 12, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('outlet_id')->constrained();
            $table->foreignId('user_id')->nullable()->constrained();
            $table->foreignId('customer_id')->nullable()->constrained();
            $table->decimal('total', 12, 2)->default(0);
            $table->unsignedInteger('items_count')->default(0);
            $table->timestamps();
        });

        Schema::create('sale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained();
            $table->integer('quantity');
            $table->decimal('price', 12, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        foreach ([
            'sale_items', 'sales', 'customers', 'purchase_order_items', 'purchase_orders',
            'suppliers', 'transfers', 'adjustments', 'stocks', 'products', 'brands',
            'categories', 'outlets',
        ] as $t) {
            Schema::dropIfExists($t);
        }
    }
};
