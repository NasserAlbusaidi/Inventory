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
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('has_variants')->default(false)->after('id');
            $table->decimal('cost_price', 10, 2)->nullable()->after('image_url');
            $table->decimal('selling_price', 10, 2)->nullable()->after('cost_price');
            $table->integer('stock_quantity')->default(0)->after('selling_price');
            $table->string('barcode')->nullable()->after('stock_quantity');
            $table->boolean('track_inventory')->default(true)->after('barcode');
            $table->unsignedBigInteger('location_id')->nullable()->after('track_inventory');
            $table->foreign('location_id')->references('id')->on('locations')->onDelete('set null');


        });

         Schema::table('product_variants', function (Blueprint $table) {
            $table->string('variant_name')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
       Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'has_variants',
                'cost_price',
                'selling_price',
                'stock_quantity',
                'track_inventory',
                'barcode',
            ]);
        });

        Schema::table('product_variants', function (Blueprint $table) {
            $table->string('variant_name')->nullable()->change();
        });
    }
};
