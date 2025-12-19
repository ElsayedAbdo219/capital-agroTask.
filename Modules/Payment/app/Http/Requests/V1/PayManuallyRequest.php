<?php

namespace Modules\Payment\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class PayManuallyRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
          'order_id' => ['required','exists:orders,id'],
          'amount' => ['required','numeric','min:0'],
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
