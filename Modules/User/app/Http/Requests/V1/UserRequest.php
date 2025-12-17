<?php

namespace Modules\User\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Modules\User\Enums\UserType;

class UserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $userTypesArray = UserType::toArray();
        $userTypes = implode(',', $userTypesArray);

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'unique:users,'.$this->user],
            'password' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:'.$userTypes],
            'is_active' => ['nullable', 'boolean', 'in:0,1']
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
