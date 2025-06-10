<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales_order_items', function (Blueprint $table) {
            $table->string('saleable_type')->after('sales_order_id');
            $table->unsignedBigInteger('saleable_id')->after('saleable_type');


            $table->dropForeign(['product_variant_id']);
            $table->dropColumn('product_variant_id');
        });
    }

    public function down(): void
    {
        Schema::table('sales_order_items', function (Blueprint $table) {
            $table->foreignId('product_variant_id')->constrained()->onDelete('cascade');
            $table->dropColumn(['saleable_type', 'saleable_id']);
        });
    }
};
