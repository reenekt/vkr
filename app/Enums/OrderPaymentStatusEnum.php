<?php

namespace App\Enums;

enum OrderPaymentStatusEnum : int
{
    /** Заказ не оплачен (или ожидает оплаты) */
    case NOT_PAID = 1;

    /** Заказ оплачен */
    case PAID = 2;

    /** Ошибка при оплате (картой онлайн) */
    case FAILED = 3;
}
