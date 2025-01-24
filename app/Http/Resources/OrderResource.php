<?php

namespace App\Http\Resources;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Order */
class OrderResource extends JsonResource
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
            'customer_id' => $this->customer_id,
            'status' => $this->status,
            'payment_method' => $this->payment_method,
            'payment_system' => $this->payment_system,
            'payment_status' => $this->payment_status,
            'delivery_method' => $this->delivery_method,
            'need_design_service' => $this->need_design_service,
            'need_montage_service' => $this->need_montage_service,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'total_price' => $this->whenAppended('total_price'),
            'total_base_price' => $this->whenAppended('total_base_price'),

            'items' => OrderItemResource::collection($this->whenLoaded('items')),
            'deliveries' => DeliveryResource::collection($this->whenLoaded('deliveries')),
        ];
    }
}
