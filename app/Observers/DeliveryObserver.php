<?php

namespace App\Observers;

use App\Enums\DeliveryStatusEnum;
use App\Enums\OrderPaymentStatusEnum;
use App\Enums\OrderStatusEnum;
use App\Models\Delivery;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;

class DeliveryObserver implements ShouldHandleEventsAfterCommit
{
    /**
     * Handle the Delivery "created" event.
     */
    public function created(Delivery $delivery): void
    {
        //
    }

    /**
     * Handle the Delivery "updated" event.
     */
    public function updated(Delivery $delivery): void
    {
        // Если завершили текущую доставку (если она последняя, которая была не завершена ранее - завершаем заказ)
        if ($delivery->wasChanged('delivery_status') && $delivery->delivery_status === DeliveryStatusEnum::DELIVERED) {
            $order = $delivery->order;

            // если есть другие доставки, которые еще не завершены (то есть текущая доставка не является последней завершенной), то пропускаем смену статуса заказа
            if ($order->deliveries()->where('delivery_status', '!=', DeliveryStatusEnum::DELIVERED)->exists()) {
                return;
            }

            // меняем статус заказа на "Завершен" если все доставки доставлены и заказ оплачен
            if ($order->payment_status === OrderPaymentStatusEnum::PAID) {
                $order->status = OrderStatusEnum::DONE;
                $order->save();
            }
        }
    }

    /**
     * Handle the Delivery "deleted" event.
     */
    public function deleted(Delivery $delivery): void
    {
        //
    }

    /**
     * Handle the Delivery "restored" event.
     */
    public function restored(Delivery $delivery): void
    {
        //
    }

    /**
     * Handle the Delivery "force deleted" event.
     */
    public function forceDeleted(Delivery $delivery): void
    {
        //
    }
}
