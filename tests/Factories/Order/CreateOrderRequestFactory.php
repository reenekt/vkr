<?php

namespace Tests\Factories\Order;

use App\Enums\OrderDeliveryMethodEnum;
use App\Enums\OrderPaymentMethodEnum;
use Tests\Factories\BaseRequestDataFactory;

class CreateOrderRequestFactory extends BaseRequestDataFactory
{
    public function definition(): array
    {
        return [
            'customer_id' => $this->faker->uuid(),
            'payment_method' => $this->faker->randomElement(OrderPaymentMethodEnum::cases())->value,
            'payment_system' => null, // TODO
            'delivery_method' => $this->faker->randomElement(OrderDeliveryMethodEnum::cases())->value,
            'deliveries' => [
                [
                    'delivery_price' => $this->faker->randomFloat(2, 0, 1000),
                    'pickup_store_id' => $this->faker->uuid(),
                    'delivery_company_id' => $this->faker->uuid(),
                    'delivery_company_data' => null,
                    'items' => [
                        [
                            'product_id' => $this->faker->uuid(),
                            'quantity' => $this->faker->numberBetween(1, 10),
                            'base_price_per_unit' => $basePrice = $this->faker->randomFloat(2, 400, 12000),
                            'price_per_unit' => round($basePrice * ($this->faker->numberBetween(50, 100) / 100), 2),
                        ],
                    ],
                ]
            ],
        ];
    }

    public function selfPickup(): CreateOrderRequestFactory
    {
        return $this->state(function (array $attributes) {
            $attributes['delivery_method'] = OrderDeliveryMethodEnum::SELF_PICKUP;

            foreach ($attributes['deliveries'] as &$delivery) {
                $delivery['delivery_price'] = 0;
                $delivery['pickup_store_id'] = $this->faker->uuid();
                $delivery['delivery_company_id'] = null;
                $delivery['delivery_company_data'] = null;
            }

            return $attributes;
        });
    }

    public function cashPostPayment(): CreateOrderRequestFactory
    {
        return $this->state([
            'payment_method' => OrderPaymentMethodEnum::CASH_POST,
            'payment_system' => null,
        ]);
    }
}
