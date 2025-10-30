<?php

namespace App\Http\Requests\Project;

use App\Enum\ProjectStatus;
use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rules\Enum;

class ProjectCreateRequest extends BaseRequest
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
            'description' => ['string'],
            'status' => ['required', new Enum(ProjectStatus::class)],
            'start_date' => ['sometimes', 'required', 'date', 'date_format:Y-m-d'],
            'end_date' => ['sometimes', 'required', 'date', 'date_format:Y-m-d', 'after:start_date'],
        ];
    }
}
