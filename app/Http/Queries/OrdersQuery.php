<?php

namespace App\Http\Queries;

use App\Models\Order;

class OrdersQuery extends BaseQuery
{
    protected function configureBaseQuery(): void
    {
        $this->baseQuery = Order::query()->with(['items', 'deliveries', 'deliveries.items']);
    }

    public function allowedFilters(): array
    {
        return [
            'id' => 'exact',
            'customer_id' => 'exact',
            'status' => 'exact',
            'payment_method' => 'exact',
            'payment_system' => 'exact',
            'payment_status' => 'exact',
            'delivery_method' => 'exact',
            'need_design_service' => 'exact',
            'need_montage_service' => 'exact',
            'created_at' => 'datetime_range',
            'updated_at' => 'datetime_range',
        ];
    }
}
