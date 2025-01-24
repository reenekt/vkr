<?php

namespace App\Http\Requests;

use App\Enums\DeliveryStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateDeliveryRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'delivery_status' => ['required', new Enum(DeliveryStatusEnum::class)],
        ];
    }
}
