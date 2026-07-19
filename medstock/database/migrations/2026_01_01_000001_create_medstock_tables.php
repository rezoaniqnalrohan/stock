<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// ponytail: all domain tables in one migration — fresh DB, no incremental history to preserve.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->timestamps();
        });

        Schema::create('units', function (Blueprint $t) {
            $t->id();
            $t->string('name');           // Box, Piece, Pair, Bottle...
            $t->string('abbreviation', 12);
            $t->timestamps();
        });

        Schema::create('warehouses', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('location')->nullable();
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
            $t->string('type')->default('clinic'); // clinic | pharmacy | hospital
            $t->string('email')->nullable();
            $t->string('phone')->nullable();
            $t->string('address')->nullable();
            $t->timestamps();
        });

        Schema::create('products', function (Blueprint $t) {
            $t->id();
            $t->string('sku')->unique();
            $t->string('name');
            $t->foreignId('category_id')->constrained()->cascadeOnDelete();
            $t->foreignId('unit_id')->constrained()->cascadeOnDelete();
            $t->foreignId('supplier_id')->nullable()->constrained()->nullOnDelete();
            $t->string('barcode')->nullable();
            $t->decimal('cost', 10, 2)->default(0);
            $t->decimal('price', 10, 2)->default(0);
            $t->integer('reorder_point')->default(0);
            $t->text('description')->nullable();
            $t->timestamps();
        });

        // Inventory lot: carries multi-warehouse + lot/expiry + quantity in one table.
        Schema::create('batches', function (Blueprint $t) {
            $t->id();
            $t->foreignId('product_id')->constrained()->cascadeOnDelete();
            $t->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $t->string('lot_number')->nullable();
            $t->date('expiry_date')->nullable();
            $t->integer('quantity')->default(0);
            $t->timestamps();
        });

        Schema::create('stock_movements', function (Blueprint $t) {
            $t->id();
            $t->foreignId('product_id')->constrained()->cascadeOnDelete();
            $t->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $t->string('type');               // in | out | adjust | transfer
            $t->integer('quantity');          // signed: +in / -out
            $t->string('reference')->nullable(); // PO-12, SO-8, Adjustment, Transfer
            $t->string('note')->nullable();
            $t->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $t->timestamps();
        });

        Schema::create('purchase_orders', function (Blueprint $t) {
            $t->id();
            $t->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $t->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $t->string('status')->default('draft'); // draft | ordered | received
            $t->date('order_date');
            $t->decimal('total', 12, 2)->default(0);
            $t->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $t->timestamps();
        });

        Schema::create('purchase_order_items', function (Blueprint $t) {
            $t->id();
            $t->foreignId('purchase_order_id')->constrained()->cascadeOnDelete();
            $t->foreignId('product_id')->constrained()->cascadeOnDelete();
            $t->integer('quantity');
            $t->decimal('cost', 10, 2);
            $t->string('lot_number')->nullable();
            $t->date('expiry_date')->nullable();
            $t->timestamps();
        });

        Schema::create('sales_orders', function (Blueprint $t) {
            $t->id();
            $t->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $t->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $t->string('status')->default('pending'); // pending | picked | packed | shipped
            $t->date('order_date');
            $t->decimal('total', 12, 2)->default(0);
            $t->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $t->timestamps();
        });

        Schema::create('sales_order_items', function (Blueprint $t) {
            $t->id();
            $t->foreignId('sales_order_id')->constrained()->cascadeOnDelete();
            $t->foreignId('product_id')->constrained()->cascadeOnDelete();
            $t->integer('quantity');
            $t->decimal('price', 10, 2);
            $t->timestamps();
        });
    }

    public function down(): void
    {
        foreach ([
            'sales_order_items', 'sales_orders', 'purchase_order_items', 'purchase_orders',
            'stock_movements', 'batches', 'products', 'customers', 'suppliers',
            'warehouses', 'units', 'categories',
        ] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
