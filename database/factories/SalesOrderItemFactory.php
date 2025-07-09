<?php

namespace Database\Factories;

use App\Models\Product;
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
    protected $model = SalesOrderItem::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $saleable = $this->faker->randomElement([
            Product::class,
            ProductVariant::class,
        ]);

        $saleableInstance = $saleable::factory()->create();

        return [
            'sales_order_id' => SalesOrder::factory(),
            'saleable_id' => $saleableInstance->id,
            'saleable_type' => $saleable,
            'quantity' => $this->faker->numberBetween(1, 5),
            'price_per_unit' => $saleableInstance->selling_price,
        ];
    }
}
