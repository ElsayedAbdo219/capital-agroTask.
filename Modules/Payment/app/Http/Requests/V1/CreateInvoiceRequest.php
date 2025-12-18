<?php

namespace Modules\Payment\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateInvoiceRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
          'order_id' => ['required','exists:orders,id'],
          'amount' => ['required','string','min:1'],
          'method' => ['required','string'], // in : Mastercard and Visa. , Mobile wallets
          'currency' => ['required','in:USD, EGP, SR, AED, KWD, QAR, BHD'], // in USD, EGP, SR, AED, KWD, QAR, BHD
          'payment_url' => ['required','string','url'],
          'payment_method_id' => ['required','numeric','in:2,3,4,12,14']
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
