<?php

namespace Modules\Product\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Product\Enums\AnimalTypes;
use Modules\Product\Enums\FeedTypes;
use Modules\Product\Enums\ProductStatus;

class ProductRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $productTypes = implode(',',ProductStatus::toArray());
        $feedTypes = implode(',',FeedTypes::toArray());
        $animalTypes = implode(',', AnimalTypes::toArray());

        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'sku' => ['required', 'string', 'max:100', 'unique:products,sku' . ($this->product ? ',' . $this->product->id : '')],
            'price' => ['required', 'numeric', 'min:1'],
            'tax' => ['nullable', 'numeric', 'min:1', 'max:100'],
            'additional_data' => ['nullable', 'json'],
            'feed_type' => ['required', 'string', 'in:'.$feedTypes],
            'animal_type' => ['required', 'string', 'in:'.$animalTypes],
            'weight_per_unit' => ['required', 'numeric', 'min:1'],
            'is_returnable' => ['boolean'],
            'status' => ['nullable', 'string', 'in:'.$productTypes],
            'quantity' => ['required', 'numeric', 'min:1'],
            'batch_no' => ['required', 'numeric'],
            'expiry_date' =>  ['required','after:today'],
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
