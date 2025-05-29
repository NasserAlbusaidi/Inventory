<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InventoryMovement>
 */
class InventoryMovementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_variant_id' => \App\Models\ProductVariant::factory(),
            'location_id' => \App\Models\Location::factory(),
            'type' => $this->faker->randomElement(['stock_adjustment', 'transfer', 'purchase', 'sale']),
            'quantity' => $this->faker->numberBetween(1, 100),
            'reason' => $this->faker->sentence(),
            'reference_type' => $this->faker->randomElement(['purchase_order', 'sales_order', 'transfer_order']),
            'reference_id' => $this->faker->numberBetween(1, 1000), // Assuming reference IDs are between 1 and 1000'
            'user_id' => \App\Models\User::factory(),
        ];
    }
}
