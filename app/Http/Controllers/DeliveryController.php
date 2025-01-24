<?php

namespace App\Http\Controllers;

use App\Actions\Order\UpdateDeliveryAction;
use App\Http\Requests\UpdateDeliveryRequest;
use App\Http\Resources\DeliveryResource;
use App\Models\Delivery;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DeliveryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        return DeliveryResource::collection(Delivery::query()->with(['order', 'items'])->paginate(10));
    }

    /**
     * Display the specified resource.
     */
    public function show(Delivery $delivery): DeliveryResource
    {
        return DeliveryResource::make($delivery->loadMissing(['order', 'items']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDeliveryRequest $request, Delivery $delivery, UpdateDeliveryAction $action): DeliveryResource
    {
        $delivery = $action->execute($delivery, $request->validated());

        return DeliveryResource::make($delivery->loadMissing(['order', 'items']));
    }
}
