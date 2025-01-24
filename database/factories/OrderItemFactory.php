<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderItem>
 */
class OrderItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $basePrice = $this->faker->randomFloat(2, 400, 12000);
        $price = round($basePrice * ($this->faker->numberBetween(50, 100) / 100), 2);

        return [
            'order_id' => Order::factory(),
            'product_id' => $this->faker->uuid(),
            'quantity' => $this->faker->numberBetween(1, 6),
            'price_per_unit' => $price,
            'base_price_per_unit' => $basePrice,
        ];
    }
}
