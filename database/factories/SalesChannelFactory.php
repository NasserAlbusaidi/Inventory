<?php

namespace Database\Factories;

use App\Models\SalesChannel;
use Illuminate\Database\Eloquent\Factories\Factory;

class SalesChannelFactory extends Factory
{
    protected $model = SalesChannel::class;

    public function definition()
    {
        return [
            'name' => $this->faker->company,
        ];
    }
}
