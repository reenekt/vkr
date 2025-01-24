<?php

namespace Database\Factories;

use App\Enums\OrderDeliveryMethodEnum;
use App\Enums\OrderPaymentMethodEnum;
use App\Enums\OrderPaymentStatusEnum;
use App\Enums\OrderStatusEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_id' => $this->faker->uuid(),
            'status' => OrderStatusEnum::CREATED,
            'payment_method' => OrderPaymentMethodEnum::CASH_POST,
            'payment_system' => null,
            'payment_status' => OrderPaymentStatusEnum::NOT_PAID,
            'delivery_method' => OrderDeliveryMethodEnum::SELF_PICKUP,
        ];
    }
}
