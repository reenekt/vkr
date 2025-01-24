<?php

namespace App\Observers;

use App\Enums\DeliveryStatusEnum;
use App\Enums\OrderPaymentStatusEnum;
use App\Enums\OrderStatusEnum;
use App\Models\Order;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;

class OrderObserver implements ShouldHandleEventsAfterCommit
{
    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "updating" event.
     */
    public function updating(Order $order): void
    {
        // Если оплата не удалась - сразу отменяем заказ (не самое лучшее решение, но такая реализация является быстрой в плане написания кода)
        if ($order->isDirty('payment_status') && $order->payment_status === OrderPaymentStatusEnum::FAILED) {
            $order->status = OrderStatusEnum::CANCELLED;
        }
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        if (
            $order->wasChanged('payment_status')
            && !$order->wasChanged('status')
            && $order->payment_status === OrderPaymentStatusEnum::PAID
        ) {
            // если все доставки завершены, то завершаем заказ
            if ($order->deliveries()->where('delivery_status', '!=', DeliveryStatusEnum::DELIVERED)->doesntExist()) {
                $order->status = OrderStatusEnum::DONE;
                $order->save();
            }
        }
    }

    /**
     * Handle the Order "deleted" event.
     */
    public function deleted(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "restored" event.
     */
    public function restored(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "force deleted" event.
     */
    public function forceDeleted(Order $order): void
    {
        //
    }
}
