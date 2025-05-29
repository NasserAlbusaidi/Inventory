<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductVariant>
 */
class ProductVariantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => \App\Models\Product::factory(),
            'variant_name' => $this->faker->unique()->word(),
            'cost_price' => $this->faker->randomFloat(2, 1, 1000),
            'selling_price' => $this->faker->randomFloat(2, 1, 1000),
            'barcode' => $this->faker->unique()->ean13(),
            'stock_quantity' => $this->faker->numberBetween(0, 1000),
        ];
    }
}
