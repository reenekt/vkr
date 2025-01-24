<?php

namespace App\Enums;

enum OrderDeliveryMethodEnum: int
{
    /** Самовывоз */
    case SELF_PICKUP = 1;

    /** Доставка транспортной компанией (ТК) */
    case DELIVERY = 2;

    /** Самовывоз из пункта выдачи заказа (ПВЗ) */
    case PICKUP_POINT = 3;

    /** Собственная доставка с выездом бригады для монтажа оборудования */
    case OUR_DELIVERY_WITH_SERVICE = 4;
}
