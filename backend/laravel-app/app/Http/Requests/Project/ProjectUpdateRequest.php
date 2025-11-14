<?php

namespace App\Http\Requests\Project;

use App\Enum\ProjectStatus;
use App\Http\Requests\BaseRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rules\Enum;

class ProjectUpdateRequest extends BaseRequest
{
    public function authorize(): bool
    {
        Gate::authorize('update', $this->project);

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
            'name' => ['sometimes', 'required', 'string', 'max:100'],
            'description' => ['string'],
            'status' => ['sometimes', 'required', new Enum(ProjectStatus::class)],
            'start_date' => ['sometimes', 'required', 'date', 'date_format:Y-m-d'],
            'end_date' => ['sometimes', 'required', 'date', 'date_format:Y-m-d', 'after:start_date'],
        ];
    }
}
