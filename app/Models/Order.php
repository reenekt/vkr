<?php

namespace App\Models;

use App\Enums\OrderDeliveryMethodEnum;
use App\Enums\OrderPaymentMethodEnum;
use App\Enums\OrderPaymentStatusEnum;
use App\Enums\OrderStatusEnum;
use App\Observers\OrderObserver;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use Database\Factories\OrderFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id ID
 * @property string $customer_id ID покупателя
 * @property OrderStatusEnum $status Статус заказа
 * @property OrderPaymentMethodEnum $payment_method Способ оплаты
 * @property int|null $payment_system ID платежной системы (при оплате картой онлайн)
 * @property OrderPaymentStatusEnum $payment_status Статус оплаты
 * @property OrderDeliveryMethodEnum $delivery_method Способ доставки
 * @property bool $need_design_service Требуется ли услуга проектирования
 * @property bool $need_montage_service Требуется ли услуга монтажа
 * @property Carbon $created_at Дата создания
 * @property Carbon $updated_at Дата изменения
 * @property-read Collection<int,OrderItem>|OrderItem[] $items Позиции (товары) заказа
 * @property-read Collection<int,Delivery>|Delivery[] $deliveries Доставки товаров заказа
 */
#[ObservedBy([OrderObserver::class])]
class Order extends Model
{
    /** @use HasFactory<OrderFactory> */
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'status',
        'payment_method',
        'payment_system',
        'payment_status',
        'delivery_method',
        'need_design_service',
        'need_montage_service',
    ];

    protected $casts = [
        'status' => OrderStatusEnum::class,
        'payment_method' => OrderPaymentMethodEnum::class,
        'payment_status' => OrderPaymentStatusEnum::class,
        'delivery_method' => OrderDeliveryMethodEnum::class,
        'need_design_service' => 'boolean',
        'need_montage_service' => 'boolean',
    ];

    protected $appends = [
        'total_price',
        'total_base_price',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function deliveries(): HasMany
    {
        return $this->hasMany(Delivery::class);
    }

    /** @noinspection PhpUnused */
    public function getTotalPriceAttribute(): string
    {
        $itemsTotalPrice = $this->items()->sum('price_per_unit');
        $deliveryTotalPrice = $this->deliveries()->sum('delivery_price');

        $result = $itemsTotalPrice + $deliveryTotalPrice;

        return $this->asDecimal($result, 2);
    }

    /** @noinspection PhpUnused */
    public function getTotalBasePriceAttribute(): string
    {
        $itemsTotalPrice = $this->items()->sum('base_price_per_unit');
        $deliveryTotalPrice = $this->deliveries()->sum('delivery_price');

        $result = $itemsTotalPrice + $deliveryTotalPrice;

        return $this->asDecimal($result, 2);
    }
}
