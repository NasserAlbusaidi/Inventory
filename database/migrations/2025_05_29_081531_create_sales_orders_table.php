<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sales_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique()->autoIncrement();
            $table->enum('channel', ['shopify', 'boutique', 'other']);
            $table->foreignId('location_id')->nullable()->constrained()->onDelete('set null');
            $table->date('order_date');
            $table->json('customer_details')->nullable();
            $table->decimal('total_amount', 10, 2);
            $table->string('status')->default('completed');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_orders');
    }
};
