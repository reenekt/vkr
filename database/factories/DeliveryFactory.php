<?php

namespace Database\Factories;

use App\Enums\DeliveryStatusEnum;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Delivery>
 */
class DeliveryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'delivery_status' => DeliveryStatusEnum::NOT_DELIVERED,
            'delivery_price' => $this->faker->randomFloat(2, 0, 950),
            'pickup_store_id' => $this->faker->uuid(),
            'delivery_company_id' => null,
            'delivery_company_data' => null,
        ];
    }
}
