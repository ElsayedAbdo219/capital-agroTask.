<?php

namespace Modules\Delivery\Http\Requests\V1;

use Modules\User\Enums\UserType;
use Illuminate\Foundation\Http\FormRequest;
use Modules\Delivery\Enums\OrderDeliveryStatus;

class DeliveryRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $orderDeliveryStatus = implode(',',OrderDeliveryStatus::toArray());
        return [
          'order_id' => ['required','exists:orders,id'],
          'deliver_id' => ['required','exists:users,id,type:'.UserType::DELIVERY],
          'address' => ['required','string'],
          'phone' => ['required','array'],
          'phone.*' => ['required','unique:users,phone'.$this->delivery],
          'status' => ['nulable','in:'.$orderDeliveryStatus],
          'delivered_at' => ['required','date','after:now'],
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}
