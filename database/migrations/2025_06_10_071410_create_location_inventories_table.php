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
        Schema::create('location_inventories', function (Blueprint $table) {
            $table->id();
            $table->morphs('inventoriable');
            $table->foreignId('location_id')->constrained()->onDelete('cascade');

            $table->integer('stock_quantity')->default(0);


            $table->unique(['inventoriable_id', 'inventoriable_type', 'location_id'], 'location_inventory_unique_index');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('location_inventories');
    }
};
