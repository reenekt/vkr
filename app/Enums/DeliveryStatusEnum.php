<?php

namespace App\Enums;

enum DeliveryStatusEnum : int
{
    /** Не доставлен */
    case NOT_DELIVERED = 1;

    /** Заказ собирается для последующей передачи в доставку */
    case ASSEMBLING = 2;

    /** Собран и готов для передачи в доставку */
    case ASSEMBLED_AND_READY_FOR_DELIVERY = 3;

    /** Передан в доставку */
    case TRANSFERRED_FOR_DELIVERY = 4;

    /** Доставлен */
    case DELIVERED = 5;

    public static function getAllowedNext(self $currentStatus): array
    {
        $allowedNextByCurrent = [
            self::NOT_DELIVERED->value => [self::ASSEMBLING],
            self::ASSEMBLING->value => [self::ASSEMBLED_AND_READY_FOR_DELIVERY],
            self::ASSEMBLED_AND_READY_FOR_DELIVERY->value => [self::TRANSFERRED_FOR_DELIVERY, self::DELIVERED],
            self::TRANSFERRED_FOR_DELIVERY->value => [self::DELIVERED],
            self::DELIVERED->value => [],
        ];

        return $allowedNextByCurrent[$currentStatus->value] ?? [];
    }
}
