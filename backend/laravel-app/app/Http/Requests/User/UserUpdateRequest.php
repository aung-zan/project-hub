<?php

namespace App\Http\Requests\User;

use App\Http\Requests\BaseRequest;

class UserUpdateRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:100'],
            'password' => ['sometimes', 'required', 'min:8', 'max:255'],
            'confirm_password' => ['required_with:password', 'same:password'],
        ];
    }
}
