<?php

namespace App\Http\Controllers;

use App\Actions\Order\StoreOrderAction;
use App\Actions\Order\UpdateOrderAction;
use App\Http\Queries\OrdersQuery;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(OrdersQuery $query): AnonymousResourceCollection
    {
        return OrderResource::collection($query->paginate(10));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrderRequest $request, StoreOrderAction $action): OrderResource
    {
        $order = $action->execute($request->validated());

        return OrderResource::make($order->loadMissing(['items', 'deliveries', 'deliveries.items']));
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order): OrderResource
    {
        return OrderResource::make($order->loadMissing(['items', 'deliveries', 'deliveries.items']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrderRequest $request, Order $order, UpdateOrderAction $action): OrderResource
    {
        $order = $action->execute($order, $request->validated());

        return OrderResource::make($order->loadMissing(['items', 'deliveries', 'deliveries.items']));
    }
}
