<?php

namespace App\Http\Requests;

use App\Enums\OrderDeliveryMethodEnum;
use App\Enums\OrderPaymentMethodEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreOrderRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'customer_id' => ['required', 'string', 'min:1'],
            'payment_method' => ['required', new Enum(OrderPaymentMethodEnum::class)],
            'payment_system' => ['nullable', 'integer'],
            'delivery_method' => ['required', new Enum(OrderDeliveryMethodEnum::class)],

            'deliveries' => ['required', 'array', 'min:1'],
            'deliveries.*.delivery_price' => ['required', 'decimal:0,2', 'gte:0'],
            'deliveries.*.pickup_store_id' => ['nullable', 'string', 'min:1'],
            'deliveries.*.delivery_company_id' => ['nullable', 'string', 'min:1'],
            'deliveries.*.delivery_company_data' => ['nullable'],

            'deliveries.*.items' => ['required', 'array', 'min:1'],
            'deliveries.*.items.*.product_id' => ['required', 'string', 'min:1'],
            'deliveries.*.items.*.quantity' => ['required', 'integer', 'min:1'],
            'deliveries.*.items.*.price_per_unit' => ['required', 'decimal:0,2', 'gt:0'],
            'deliveries.*.items.*.base_price_per_unit' => ['required', 'decimal:0,2', 'gt:0'],
        ];
    }
}
