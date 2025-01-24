<?php

namespace App\Models;

use Database\Factories\OrderItemFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id ID
 * @property int $order_id ID заказа
 * @property string $product_id ID товара
 * @property int $quantity Количество товара
 * @property float $price_per_unit Цена за единицу товара с учетом скидки
 * @property float $base_price_per_unit Цена за единицу товара без учета скидки
 * @property Carbon $created_at Дата создания
 * @property Carbon $updated_at Дата изменения
 * @property-read Order $order Заказ
 */
class OrderItem extends Model
{
    /** @use HasFactory<OrderItemFactory> */
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'price_per_unit',
        'base_price_per_unit',
    ];

    protected $casts = [
        'price_per_unit' => 'decimal:2',
        'base_price_per_unit' => 'decimal:2',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
