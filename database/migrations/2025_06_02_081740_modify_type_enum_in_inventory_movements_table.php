<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventory_movements', function (Blueprint $table) {
            // IMPORTANT: You need to list ALL desired enum values here, including old and new ones.
            // The order might matter if you have existing data and are not on some newer MySQL versions.
            $table->enum('type', [
                'in', // Existing
                'out', // Existing
                'adjustment', // Existing
                'transfer_in', // Existing
                'transfer_out', // Existing
                'purchase_receipt', // New
                'sale', // New (if you have sales)
                'stock_take_variance', // New (if you do stock takes)
                'return_in', // New (customer return)
                'return_out' // New (supplier return)
                // Add any other types you foresee
            ])->change();
        });
    }

    public function down(): void
    {
        Schema::table('inventory_movements', function (Blueprint $table) {
            // Revert to the original ENUM definition
            $table->enum('type', [
                'in', 'out', 'adjustment', 'transfer_in', 'transfer_out'
            ])->change();
            // Be cautious with down migrations that might truncate data.
        });
    }
};
