<?php

use App\Enums\DeliveryStatusEnum;
use App\Enums\OrderPaymentMethodEnum;
use App\Enums\OrderPaymentStatusEnum;
use App\Enums\OrderStatusEnum;
use App\Models\Delivery;
use App\Models\Order;
use App\Models\OrderItem;
use function Pest\Laravel\getJson;
use function Pest\Laravel\patchJson;

test('GET /api/delivery 200', function () {
    $order = Order::factory()->create();
    $deliveries = Delivery::factory()
        ->recycle($order)
        ->for($order)
        ->has(
            OrderItem::factory()->recycle($order)->count(2),
            'items'
        )
        ->count(2)
        ->create();

    getJson('/api/delivery')
        ->assertStatus(200)
        ->assertJsonCount(2, 'data');
});

test('GET /api/delivery/{id} 200', function () {
    $order = Order::factory()->create();
    $delivery = Delivery::factory()
        ->recycle($order)
        ->for($order)
        ->has(
            OrderItem::factory()->recycle($order)->count(2),
            'items'
        )
        ->createOne();

    getJson("/api/delivery/$delivery->id")
        ->assertStatus(200)
        ->assertJsonPath('data.id', $delivery->id);
});

test('PATCH /api/delivery/{id} 200 change delivery status', function (int $oldStatus, int $newStatusValue) {
    $order = Order::factory()->create();
    $delivery = Delivery::factory()
        ->recycle($order)
        ->for($order)
        ->has(
            OrderItem::factory()->recycle($order)->count(2),
            'items'
        )
        ->createOne([
            'delivery_status' => $oldStatus,
        ]);

    $data = [
        'delivery_status' => $newStatusValue,
    ];

    patchJson("/api/delivery/$delivery->id", $data)
        ->assertStatus(200);

    $delivery->refresh();
    expect($delivery->delivery_status)->toBe(DeliveryStatusEnum::from($newStatusValue));
})->with([
    'status not delivered -> assembling' => [DeliveryStatusEnum::NOT_DELIVERED->value, DeliveryStatusEnum::ASSEMBLING->value],
    'status assembling -> assembled and ready for delivery' => [DeliveryStatusEnum::ASSEMBLING->value, DeliveryStatusEnum::ASSEMBLED_AND_READY_FOR_DELIVERY->value],
    'status assembled and ready for delivery -> transferred for delivery' => [DeliveryStatusEnum::ASSEMBLED_AND_READY_FOR_DELIVERY->value, DeliveryStatusEnum::TRANSFERRED_FOR_DELIVERY->value],
    'status assembled and ready for delivery -> delivered' => [DeliveryStatusEnum::ASSEMBLED_AND_READY_FOR_DELIVERY->value, DeliveryStatusEnum::DELIVERED->value],
    'status transferred for delivery -> delivered' => [DeliveryStatusEnum::TRANSFERRED_FOR_DELIVERY->value, DeliveryStatusEnum::DELIVERED->value],
]);

test('PATCH /api/delivery/{id} 200 change delivery status to delivered for paid order', function () {
    $order = Order::factory()->create([
        'payment_method' => OrderPaymentMethodEnum::CASH_POST,
        'payment_status' => OrderPaymentStatusEnum::PAID,
    ]);
    $delivery = Delivery::factory()
        ->recycle($order)
        ->for($order)
        ->has(
            OrderItem::factory()->recycle($order)->count(2),
            'items'
        )
        ->createOne([
            'delivery_status' => DeliveryStatusEnum::TRANSFERRED_FOR_DELIVERY,
        ]);

    $data = [
        'delivery_status' => DeliveryStatusEnum::DELIVERED->value,
    ];

    patchJson("/api/delivery/$delivery->id", $data)
        ->assertStatus(200);

    $delivery->refresh();
    expect($delivery->delivery_status)->toBe(DeliveryStatusEnum::DELIVERED)
        ->and($order->refresh()->status)->toBe(OrderStatusEnum::DONE);
});
