<?php

namespace Database\Factories;
use App\Models\Category;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $perfumeName = 'Eau de ' . $this->faker->unique()->word();
        // Ensure at least one category exists, or create one
        $category = Category::inRandomOrder()->first() ?? Category::factory()->create();

        return [
            'sku' => $this->faker->unique()->ean8(),
            'name' => $perfumeName,
            'description' => $this->faker->paragraph(),
            'category_id' => $category->id, // Changed from 'category' string
            'image_url' => $this->faker->imageUrl(640, 480, 'perfume', true),
        ];
    }
}
