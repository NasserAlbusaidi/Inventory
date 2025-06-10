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
use App\Models\Category;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;



class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create a specific test user (as you had)
        $defaultUser = User::factory()->create([ //
            'name' => 'Nasser Albusaidi',
            'email' => 'nasser@example.com', // Your email
            'password' => Hash::make('password'), // Set a default password
        ]);

        // Create a few other random users
        User::factory(5)->create();

        // Create Locations
        $locations = Location::factory(3)->create(); //

        // Create Suppliers
        $suppliers = Supplier::factory(1)->create(
            [ // You can adjust the number of suppliers as needed
                'name' => 'Default Supplier'
            ]
        );

        // Create Categories (if you create a CategoryFactory and use the string field in Product)
        // Or, if you've transitioned Product to use category_id:
        $categories = Category::factory(5)->create(); //

        // Create Products, each with a few Variants
        Product::factory(20)->create()->each(function ($product) use ($defaultUser, $locations, $categories) { // Pass $categories if needed by product factory logic directly, or ProductFactory can fetch them
            $numberOfVariants = rand(1, 3);
            for ($i = 0; $i < $numberOfVariants; $i++) {
                $variant = ProductVariant::factory()->create([
                    'product_id' => $product->id,
                ]);

                if ($variant->stock_quantity > 0) {
                    InventoryMovement::factory()->create([
                        'product_variant_id' => $variant->id,
                        'location_id' => $locations->random()->id,
                        'type' => 'adjustment',
                        'quantity' => $variant->stock_quantity,
                        'reason' => 'Initial stock seeding',
                        'user_id' => $defaultUser->id,
                    ]);
                }
            }
        });

        // Placeholder for Purchase Orders and Sales Orders (requires more detailed factories)
        // Example:
        PurchaseOrder::factory(10)->create(['supplier_id' => $suppliers->random()->id])->each(function ($po) use ($defaultUser, $locations) {
            $productVariants = ProductVariant::inRandomOrder()->take(rand(1,5))->get();
            foreach($productVariants as $variant) {
                PurchaseOrderItem::factory()->create([
                    'purchase_order_id' => $po->id,
                    'product_variant_id' => $variant->id,
                    'cost_price_per_unit' => $variant->cost_price, // Use variant's cost price
                ]);
                // Potentially create an 'in' InventoryMovement when PO is 'received'
            }
        });

        SalesOrder::factory(15)->create(['location_id' => $locations->random()->id])->each(function ($so) use ($defaultUser, $locations){
            $productVariants = ProductVariant::where('stock_quantity', '>', 0)->inRandomOrder()->take(rand(1,3))->get();
             foreach($productVariants as $variant) {
                if ($variant->stock_quantity > 0) {
                    $quantitySold = rand(1, min(5, $variant->stock_quantity)); // Sell between 1 and 5, or available stock
                    SalesOrderItem::factory()->create([
                        'sales_order_id' => $so->id,
                        'product_variant_id' => $variant->id,
                        'quantity' => $quantitySold,
                        'price_per_unit' => $variant->selling_price, // Use variant's selling price
                    ]);
                    // Decrease stock and create 'out' InventoryMovement
                    // $variant->decrement('stock_quantity', $quantitySold);
                    // InventoryMovement::factory()->create([ /* ... type 'out' ... */ ]);
                }
            }
        });

        $this->command->info('Dummy data seeded successfully!');
    }
}
