<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// ponytail: all domain tables in one migration; split only if a team needs independent rollback.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('color')->default('#7c3aed'); // for donut chart segments
            $t->timestamps();
        });

        Schema::create('units', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('abbreviation', 12);
            $t->timestamps();
        });

        Schema::create('warehouses', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('code', 12)->unique();
            $t->string('location')->nullable();
            $t->boolean('is_cold_chain')->default(false);
            $t->timestamps();
        });

        Schema::create('suppliers', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('contact_name')->nullable();
            $t->string('email')->nullable();
            $t->string('phone')->nullable();
            $t->string('address')->nullable();
            $t->timestamps();
        });

        Schema::create('customers', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('contact_name')->nullable();
            $t->string('email')->nullable();
            $t->string('phone')->nullable();
            $t->string('address')->nullable();
            $t->timestamps();
        });

        Schema::create('products', function (Blueprint $t) {
            $t->id();
            $t->string('sku')->unique();
            $t->string('name');
            $t->foreignId('category_id')->constrained();
            $t->foreignId('unit_id')->constrained();
            $t->foreignId('supplier_id')->nullable()->constrained();
            $t->decimal('price', 10, 2)->default(0);
            $t->decimal('cost', 10, 2)->default(0);
            $t->string('storage_temp')->default('ambient'); // ambient | chilled | frozen
            $t->unsignedInteger('shelf_life_days')->default(0);
            $t->unsignedInteger('reorder_point')->default(0);
            $t->timestamps();
        });

        // A batch is the physical stock holding: product x warehouse x lot, with quantity + expiry.
        Schema::create('batches', function (Blueprint $t) {
            $t->id();
            $t->foreignId('product_id')->constrained()->cascadeOnDelete();
            $t->foreignId('warehouse_id')->constrained();
            $t->string('batch_no');
            $t->integer('quantity')->default(0);
            $t->date('expiry_date')->nullable();
            $t->timestamps();
        });

        Schema::create('stock_movements', function (Blueprint $t) {
            $t->id();
            $t->foreignId('product_id')->constrained()->cascadeOnDelete();
            $t->foreignId('warehouse_id')->constrained();
            $t->string('type'); // in | out | adjustment | transfer
            $t->integer('quantity'); // signed: +in / -out
            $t->string('reference')->nullable();
            $t->string('note')->nullable();
            $t->timestamps();
        });

        Schema::create('purchase_orders', function (Blueprint $t) {
            $t->id();
            $t->string('po_number')->unique();
            $t->foreignId('supplier_id')->constrained();
            $t->foreignId('warehouse_id')->constrained();
            $t->string('status')->default('draft'); // draft | ordered | received
            $t->date('order_date');
            $t->date('expected_date')->nullable();
            $t->decimal('total', 12, 2)->default(0);
            $t->timestamps();
        });

        Schema::create('purchase_order_items', function (Blueprint $t) {
            $t->id();
            $t->foreignId('purchase_order_id')->constrained()->cascadeOnDelete();
            $t->foreignId('product_id')->constrained();
            $t->integer('quantity');
            $t->decimal('unit_cost', 10, 2);
            $t->timestamps();
        });

        Schema::create('sales_orders', function (Blueprint $t) {
            $t->id();
            $t->string('order_number')->unique();
            $t->foreignId('customer_id')->constrained();
            $t->foreignId('warehouse_id')->constrained();
            $t->string('status')->default('pending'); // pending | fulfilled | shipped
            $t->date('order_date');
            $t->decimal('total', 12, 2)->default(0);
            $t->timestamps();
        });

        Schema::create('sales_order_items', function (Blueprint $t) {
            $t->id();
            $t->foreignId('sales_order_id')->constrained()->cascadeOnDelete();
            $t->foreignId('product_id')->constrained();
            $t->integer('quantity');
            $t->decimal('unit_price', 10, 2);
            $t->timestamps();
        });

        Schema::create('shipments', function (Blueprint $t) {
            $t->id();
            $t->string('reference')->unique();
            $t->string('type'); // inbound | outbound | transfer
            $t->string('origin')->nullable();
            $t->string('destination')->nullable();
            $t->string('status')->default('pending'); // pending | in_transit | delivered
            $t->date('ship_date')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        foreach ([
            'shipments', 'sales_order_items', 'sales_orders', 'purchase_order_items',
            'purchase_orders', 'stock_movements', 'batches', 'products', 'customers',
            'suppliers', 'warehouses', 'units', 'categories',
        ] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
