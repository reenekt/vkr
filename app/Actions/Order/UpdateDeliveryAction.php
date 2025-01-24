<?php

namespace App\Actions\Order;

use App\Enums\DeliveryStatusEnum;
use App\Models\Delivery;
use Illuminate\Validation\ValidationException;

class UpdateDeliveryAction
{
    /**
     * @throws ValidationException
     */
    public function execute(Delivery $delivery, array $data): Delivery
    {
        $this->validateIfStatusCanBeChanged($delivery, DeliveryStatusEnum::from($data['delivery_status']));

        $delivery->update($data);

        return $delivery;
    }

    /**
     * @throws ValidationException
     */
    private function validateIfStatusCanBeChanged(Delivery $delivery, DeliveryStatusEnum $newStatus): void
    {
        $allowedNextStatuses = DeliveryStatusEnum::getAllowedNext($delivery->delivery_status);

        if (!in_array($newStatus, $allowedNextStatuses)) {
            throw ValidationException::withMessages([
                'delivery_status' => sprintf(
                    "Нельзя поменять статус с %s на %s (допустимые следующие статусы: %s)",
                    $delivery->delivery_status->name,
                    $newStatus->name,
                    implode(', ', array_map(fn(DeliveryStatusEnum $next) => $next->name, $allowedNextStatuses))
                )
            ]);
        }
    }
}
