<?php

namespace App\Http\Requests\TeamUser;

use App\Http\Requests\BaseRequest;
use Illuminate\Support\Facades\Gate;

class TeamUserCreateRequest extends BaseRequest
{
    public function authorize(): bool
    {
        Gate::authorize('view', $this->team);

        return true;
    }

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
