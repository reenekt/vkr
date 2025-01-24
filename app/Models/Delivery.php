<?php

namespace App\Models;

use App\Enums\DeliveryStatusEnum;
use App\Observers\DeliveryObserver;
use Database\Factories\DeliveryFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id ID
 * @property int $order_id ID заказа
 * @property DeliveryStatusEnum $delivery_status Статус доставки
 * @property float $delivery_price Стоимость доставки
 * @property string|null $pickup_store_id ID магазина/склада для самовывоза
 * @property string|null $delivery_company_id ID транспортной компании (для доставки с помощью ТК)
 * @property array|null $delivery_company_data Данные по доставке транспортной компанией (формат отличается в зависимости от ТК, требуется дл интеграции с ТК)
 * @property Carbon $created_at Дата создания
 * @property Carbon $updated_at Дата изменения
 * @property-read Order $order Заказ
 * @property-read Collection<int,OrderItem>|OrderItem[] $items Позиции (товары) текущей доставки
 */
#[ObservedBy([DeliveryObserver::class])]
class Delivery extends Model
{
    /** @use HasFactory<DeliveryFactory> */
    use HasFactory;

    protected $fillable = [
        'order_id',
        'delivery_status',
        'delivery_price',
        'pickup_store_id',
        'delivery_company_id',
        'delivery_company_data',
    ];

    protected $casts = [
        'delivery_price' => 'decimal:2',
        'delivery_status' => DeliveryStatusEnum::class,
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(OrderItem::class);
    }
}
