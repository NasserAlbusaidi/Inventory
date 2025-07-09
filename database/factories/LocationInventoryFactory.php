<?php

namespace Database\Factories;

use App\Models\LocationInventory;
use Illuminate\Database\Eloquent\Factories\Factory;

class LocationInventoryFactory extends Factory
{
    protected $model = LocationInventory::class;

    public function definition()
    {
        return [
            'stock_quantity' => $this->faker->numberBetween(1, 100),
        ];
    }
}
