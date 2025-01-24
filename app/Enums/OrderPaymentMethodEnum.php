<?php

namespace App\Enums;

enum OrderPaymentMethodEnum : int
{
    /** Банковской картой онлайн (предоплата) */
    case ONLINE = 1;

    /** Пост-оплата наличными или картой при получении заказа */
    case CASH_POST = 2;
}
