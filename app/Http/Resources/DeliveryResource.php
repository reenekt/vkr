<?php

namespace App\Http\Resources;

use App\Models\Delivery;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Delivery */
class DeliveryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_id' => $this->order_id,
            'delivery_status' => $this->delivery_status,
            'delivery_price' => $this->delivery_price,
            'pickup_store_id' => $this->pickup_store_id,
            'delivery_company_id' => $this->delivery_company_id,
            'delivery_company_data' => $this->delivery_company_data,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'order' => $this->whenLoaded('order'),
            'items' => $this->whenLoaded('items'),
        ];
    }
}
