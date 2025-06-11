<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Location;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\InventoryMovement;
use Illuminate\Support\Facades\Hash;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\SalesChannel;
use App\Models\Category;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use Illuminate\Support\Facades\Log;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // create sales channels
        SalesChannel::firstOrCreate(['name' => 'Website']);
        SalesChannel::firstOrCreate(['name' => 'Instagram']);
        SalesChannel::firstOrCreate(['name' => 'WhatsApp']);
        SalesChannel::firstOrCreate(['name' => 'Boutique Sale']);
        SalesChannel::firstOrCreate(['name' => 'Other']);

        Log::info('Sales channels created successfully.');
        // create three locations
        Location::firstOrCreate(['name' => 'Main Boutique', 'description' => '123 Main St, City, Country']);
        Location::firstOrCreate(['name' => 'Warehouse', 'description' => '456 Warehouse Rd, City, Country']);
        Location::firstOrCreate(['name' => 'Online Store', 'description' => 'N/A']);
        Log::info('Locations created successfully.');
        // create three suppliers
        Supplier::firstOrCreate(['name' => 'Supplier A']);
        Supplier::firstOrCreate(['name' => 'Supplier B']);
        Supplier::firstOrCreate(['name' => 'Supplier C']);
        Log::info('Suppliers created successfully.');
        // create three categories
        Category::firstOrCreate(['name' => 'Skincare']);
        Category::firstOrCreate(['name' => 'Makeup']);
        Category::firstOrCreate(['name' => 'Haircare']);

        Log::info('Categories created successfully.');
        // create three products
        Product::firstOrCreate([
            'name' => 'Moisturizer',
            'description' => 'Hydrating moisturizer for all skin types.',
            'sku' => 'MST-001',
            'category_id' => 1, // Skincare
            'has_variants' => true,
        ]);
        Product::firstOrCreate([
            'name' => 'Lipstick',
            'description' => 'Long-lasting lipstick in various shades.',
            'sku' => 'LIP-001',
            'category_id' => 2, // Makeup
            'has_variants' => true,
        ]);
        Product::firstOrCreate([
            'name' => 'Shampoo',
            'description' => 'Gentle shampoo for daily use.',
            'sku' => 'SHAM-001',
            'category_id' => 3, // Haircare
            'has_variants' => false,
            'cost_price' => 10.00,
        ]);
        Log::info('Products created successfully.');
        // create three product variants
        ProductVariant::firstOrCreate([
            'product_id' => 1, // Moisturizer
            'variant_name' => '50ml',
            'cost_price' => 10.00,
            'selling_price' => 12.00,

        ]);
        ProductVariant::firstOrCreate([
            'product_id' => 2, // Lipstick
            'variant_name' => 'Red',
            'cost_price' => 5.00,
            'selling_price' => 8.00,
        ]);
        ProductVariant::firstOrCreate([
            'product_id' => 2, // Lipstick
            'variant_name' => '250ml',
            'cost_price' => 15.00,
            'selling_price' => 20.00,
        ]);



        $this->command->info('Dummy data seeded successfully!');
    }
}
