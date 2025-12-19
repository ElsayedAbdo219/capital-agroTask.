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
        $userTypesArray = implode(',', UserType::toArray());

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'unique:users,email,' . $this->route('user')->id],
            'password' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:'.$userTypesArray],
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
