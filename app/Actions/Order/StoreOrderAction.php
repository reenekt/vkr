<?php

namespace App\Actions\Order;

use App\Enums\DeliveryStatusEnum;
use App\Enums\OrderPaymentStatusEnum;
use App\Enums\OrderStatusEnum;
use App\Models\Delivery;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class StoreOrderAction
{
    public function execute(array $data): Order
    {
        return DB::transaction(function () use ($data) {
            $order = $this->createOrder($data);

            $this->createDeliveries($order, $data['deliveries']);

            return $order;
        });
    }

    private function createOrder(array $data): Order
    {
        return Order::query()->create([
            'customer_id' => $data['customer_id'],
            'status' => OrderStatusEnum::CREATED,
            'payment_method' => $data['payment_method'],
            'payment_system' => $data['payment_system'],
            'payment_status' => OrderPaymentStatusEnum::NOT_PAID,
            'delivery_method' => $data['delivery_method'],
            'need_design_service' => $data['need_design_service'],
            'need_montage_service' => $data['need_montage_service'],
        ]);
    }

    private function createDeliveries(Order $order, array $deliveriesData): void
    {
        foreach ($deliveriesData as $deliveryData) {
            $delivery = $order->deliveries()->create([
                'delivery_status' => DeliveryStatusEnum::NOT_DELIVERED,
                'delivery_price' => $deliveryData['delivery_price'],
                'pickup_store_id' => $deliveryData['pickup_store_id'],
                'delivery_company_id' => $deliveryData['delivery_company_id'],
                'delivery_company_data' => $deliveryData['delivery_company_data'],
            ]);

            // Создаём записи в БД о позициях заказа, связанных с текущей доставкой
            $this->createDeliveryOrderItems($delivery, $deliveryData['items']);
        }

    }

    private function createDeliveryOrderItems(Delivery $delivery, array $deliveryOrderItemsData): void
    {
        $delivery->items()->createMany(array_map(fn(array $itemData) => [
            'order_id' => $delivery->order_id,
            'product_id' => $itemData['product_id'],
            'quantity' => $itemData['quantity'],
            'price_per_unit' => $itemData['price_per_unit'],
            'base_price_per_unit' => $itemData['base_price_per_unit'],
        ], $deliveryOrderItemsData));
    }
}
