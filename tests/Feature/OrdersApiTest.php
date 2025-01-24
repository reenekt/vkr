<?php

use App\Enums\DeliveryStatusEnum;
use App\Enums\OrderPaymentMethodEnum;
use App\Enums\OrderPaymentStatusEnum;
use App\Enums\OrderStatusEnum;
use App\Models\Delivery;
use App\Models\Order;
use App\Models\OrderItem;
use Tests\Factories\Order\CreateOrderRequestFactory;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\getJson;
use function Pest\Laravel\patchJson;
use function Pest\Laravel\postJson;

test('GET /api/order 200', function () {
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

    getJson('/api/order')
        ->assertStatus(200)
        ->assertJsonCount(1, 'data');
});

test('GET /api/order/{id} 200', function () {
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

    getJson("/api/order/$order->id")
        ->assertStatus(200)
        ->assertJsonPath('data.id', $order->id);
});

test('POST /api/order 201 self-pickup and cash post-payment', function () {
    $data = CreateOrderRequestFactory::new()->selfPickup()->cashPostPayment()->make();

    $id = postJson('/api/order', $data)
        ->assertStatus(201)
        ->json('data.id');

    assertDatabaseHas(Order::class, ['id' => $id]);

    $order = Order::query()->findOrFail($id);

    expect($order->deliveries()->count())->toBeGreaterThan(0)
        ->and($order->items()->count())->toBeGreaterThan(0);
});

test('PATCH /api/order/{id} 200 change payment status to failed', function () {
    $order = Order::factory()->create([
        'payment_method' => OrderPaymentMethodEnum::CASH_POST,
        'payment_status' => OrderPaymentStatusEnum::NOT_PAID,
    ]);
    $deliveries = Delivery::factory()
        ->recycle($order)
        ->for($order)
        ->has(
            OrderItem::factory()->recycle($order)->count(2),
            'items'
        )
        ->count(2)
        ->create();

    $data = [
        'payment_status' => OrderPaymentStatusEnum::FAILED->value,
    ];

    patchJson("/api/order/$order->id", $data)
        ->assertStatus(200);

    $order->refresh();
    expect($order->payment_status)->toBe(OrderPaymentStatusEnum::FAILED)
        ->and($order->status)->toBe(OrderStatusEnum::CANCELLED);
});

test('PATCH /api/order/{id} 200 change payment status to paid with all completed deliveries', function (bool $allDeliveriesAreCompleted) {
    $order = Order::factory()->create([
        'payment_method' => OrderPaymentMethodEnum::CASH_POST,
        'payment_status' => OrderPaymentStatusEnum::NOT_PAID,
    ]);
    $deliveries = Delivery::factory()
        ->recycle($order)
        ->for($order)
        ->has(
            OrderItem::factory()->recycle($order)->count(2),
            'items'
        )
        ->count(2)
        ->state(function () use ($allDeliveriesAreCompleted) {
            if (!$allDeliveriesAreCompleted) {
                return [];
            }

            return [
                'delivery_status' => DeliveryStatusEnum::DELIVERED,
            ];
        })
        ->create();

    $data = [
        'payment_status' => OrderPaymentStatusEnum::PAID->value,
    ];

    patchJson("/api/order/$order->id", $data)
        ->assertStatus(200);

    $order->refresh();
    expect($order->payment_status)->toBe(OrderPaymentStatusEnum::PAID)
        ->and($order->status)->toBe($allDeliveriesAreCompleted ? OrderStatusEnum::DONE : $order->status);
})->with([
    'all deliveries are completed' => [true],
    'not all deliveries are completed' => [false],
]);
