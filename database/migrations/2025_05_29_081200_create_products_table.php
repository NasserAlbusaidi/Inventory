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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('sku')->unique()->nullable();
            $table->string('name');
            $table->text('description')->nullable();
            // $table->string('category')->nullable(); // Old column, ensure it's removed if migrate:fresh wasn't fully successful before

            // Define the column first
            $table->unsignedBigInteger('category_id')->nullable(); // Ensure it's nullable here

            $table->string('image_url')->nullable();
            $table->timestamps();

            // Then add the foreign key constraint
            $table->foreign('category_id')
                  ->references('id')
                  ->on('categories')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Check if the foreign key exists before trying to drop it
            // The naming convention for foreign keys is typically: tablename_columnname_foreign
            $foreignKeys = collect(DB::select("SHOW CREATE TABLE products"))
                ->pluck('Create Table')
                ->map(function ($createSql) {
                    preg_match_all('/CONSTRAINT `(.*?)` FOREIGN KEY \(`category_id`\) REFERENCES `categories` \(`id`\)/', $createSql, $matches);
                    return $matches[1] ?? [];
                })
                ->flatten()
                ->toArray();

            foreach ($foreignKeys as $foreignKey) {
                if (str_contains($foreignKey, 'category_id_foreign')) { // Check if it's the category_id foreign key
                     $table->dropForeign($foreignKey);
                }
            }

            if (Schema::hasColumn('products', 'category_id')) {
                $table->dropColumn('category_id');
            }
        });
        // Original drop, might be reinstated if the above granular drop doesn't work as expected
        // or if you simply prefer migrate:fresh behavior.
        // Schema::dropIfExists('products');
    }
};
