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
        Schema::create('activity', function (Blueprint $table) {
            $table->id();
            $table->string('type')->comment('Type of activity, e.g., "login", "logout", "purchase"');
            $table->text('description')->nullable()->comment('Detailed description of the activity');
            $table->timestamp('activity_time')->useCurrent()->comment('Timestamp of when the activity occurred');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity');
    }
};
