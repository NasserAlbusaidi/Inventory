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
        Schema::table('sales_orders', function (Blueprint $table) {
            $table->foreignId('sales_channel_id')->nullable()->after('order_number')->constrained('sales_channels');

            $table->dropColumn('channel');

            $table->unsignedBigInteger('location_id')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::table('sales_orders', function (Blueprint $table) {
            $table->string('channel')->after('order_number');
            $table->dropConstrainedForeignId('sales_channel_id');
            $table->unsignedBigInteger('location_id')->nullable()->change();
        });
    }
};
