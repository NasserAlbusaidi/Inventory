<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PurchaseOrder>
 */
class PurchaseOrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'supplier_id' => \App\Models\Supplier::factory(),
            'order_number' => $this->faker->unique()->numerify('PO#####'),
            'expected_delivery_date' => $this->faker->dateTimeBetween('now', '+1 month'),

            'order_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'status' => $this->faker->randomElement(['draft', 'approved', 'ordered', 'partially_received', 'received', 'cancelled']),
            'total_amount' => $this->faker->numberBetween(100, 10000),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
