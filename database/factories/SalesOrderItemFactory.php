<?php

namespace Database\Factories;

use App\Models\ProductVariant;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SalesOrderItem>
 */
class SalesOrderItemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SalesOrderItem::class; //

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Ensure ProductVariants with stock exist
        $productVariant = ProductVariant::where('stock_quantity', '>', 0)->inRandomOrder()->first() ?? ProductVariant::factory()->create(['stock_quantity' => $this->faker->numberBetween(10, 50)]); //

        $quantitySold = 1;
        if ($productVariant->stock_quantity > 0) {
            $quantitySold = $this->faker->numberBetween(1, min(5, $productVariant->stock_quantity));
        }


        return [
            'sales_order_id' => SalesOrder::factory(), // Assumes a SalesOrder will be created or is passed
            'product_variant_id' => $productVariant->id,
            'quantity' => $quantitySold,
            'price_per_unit' => $productVariant->selling_price, // Use the variant's selling price
        ];
    }
}
