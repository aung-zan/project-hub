<?php

namespace App\Http\Requests\Team;

use App\Http\Requests\BaseRequest;
use Illuminate\Foundation\Http\FormRequest;

class TeamUpdateRequest extends BaseRequest
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
            'description' => ['string'],
        ];
    }
}
