<?php

namespace App\Enums;

enum OrderStatusEnum : int
{
    /** Заказ создан/принят в работу */
    case CREATED = 1;

    /** Заказ завершен */
    case DONE = 2;

    /** Заказ отменён */
    case CANCELLED = 3;
}
