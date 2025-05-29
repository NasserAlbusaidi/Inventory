<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\PurchaseOrder;
use App\Models\ProductVariant;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PurchaseOrderItem>
 */
class PurchaseOrderItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $productVariant = ProductVariant::inRandomOrder()->first() ?? ProductVariant::factory()->create(); //

        return [
            'purchase_order_id' => PurchaseOrder::factory(), // Assumes a PurchaseOrder will be created or is passed
            'product_variant_id' => $productVariant->id,
            'quantity' => $this->faker->numberBetween(1, 20),
            'cost_price_per_unit' => $productVariant->cost_price, // Use the variant's cost price
        ];
    }
}
