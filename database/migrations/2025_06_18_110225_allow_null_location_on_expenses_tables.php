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
        Schema::table('recurring_expenses', function (Blueprint $table) {
            $table->foreignId('location_id')->nullable()->change();
        });

        Schema::table('one_time_expenses', function (Blueprint $table) {
            $table->foreignId('location_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::table('recurring_expenses', function (Blueprint $table) {
            $table->foreignId('location_id')->nullable(false)->change();
        });

        Schema::table('one_time_expenses', function (Blueprint $table) {
            $table->foreignId('location_id')->nullable(false)->change();
        });
    }
};
