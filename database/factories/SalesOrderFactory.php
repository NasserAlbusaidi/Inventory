<?php

namespace Database\Factories;

use App\Models\SalesChannel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SalesOrder>
 */
class SalesOrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_number' => $this->faker->unique()->numerify('SO#####'),
            'sales_channel_id' => SalesChannel::factory(),
            'location_id' => \App\Models\Location::factory(),
            'order_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'customer_details' => $this->faker->name(),
            'total_amount' => $this->faker->numberBetween(100, 10000),
            'status' => $this->faker->randomElement(['draft', 'confirmed', 'shipped', 'delivered', 'cancelled']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
