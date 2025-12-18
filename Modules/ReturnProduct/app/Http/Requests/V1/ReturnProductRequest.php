<?php

namespace Modules\ReturnProduct\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Modules\ReturnProduct\Enums\ReturnProductStatus;

class ReturnProductRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
      $returnedProducts = implode(',',ReturnProductStatus::toArray());
        return [
          'order_item_id' =>['required','exists:order_items,id'],
          'quantity' =>['required','numeric','min:1'],
          'reason' =>['required','string'],
          'status' =>['nullable','in:'.$returnedProducts],
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
