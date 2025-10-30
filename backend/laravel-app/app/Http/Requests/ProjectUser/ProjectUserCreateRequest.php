<?php

namespace App\Http\Requests\ProjectUser;

use App\Http\Requests\BaseRequest;

class ProjectUserCreateRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'member_id' => ['required', 'array'],
            'member_id.*' => ['required', 'integer:strict', 'min:1', 'exists:users,id'],
        ];
    }

    public function attributes(): array
    {
        return [
            'member_id' => 'member_id',
            'member_id.*' => 'member_id',
        ];
    }
}
