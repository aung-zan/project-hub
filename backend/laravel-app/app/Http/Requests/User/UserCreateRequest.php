<?php

namespace App\Http\Requests\User;

use App\Http\Requests\BaseRequest;

class UserCreateRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'max:100', 'unique:users'],
            'email' => ['required', 'max:100', 'email:dns', 'unique:users'],
            'password' => ['required', 'min:8', 'max:255'],
            'confirm_password' => ['required', 'same:password'],
        ];
    }
}
