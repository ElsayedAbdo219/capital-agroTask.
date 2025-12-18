<?php

namespace Modules\Order\Http\Requests\V1;

use Modules\User\Enums\UserType;
use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
          'user_id' => ['required','exists:users,id,type:'.UserType::CLIENT],
          'total_amount' => ['required','numeric',"min:1"],
          'product_id' => ['required','exists:products,id'],
          'unit_price' => ['required','numeric',"min:1"],
          'total_amount' => ['required','numeric',"min:1"],

          
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
