<?php

namespace App\Http\Requests\Task;

use App\Enum\TaskPriority;
use App\Enum\TaskStatus;
use App\Http\Requests\BaseRequest;
use App\Models\Task;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rules\Enum;

class TaskCreateRequest extends BaseRequest
{
    public function authorize(): bool
    {
        Gate::authorize('create', [Task::class, $this->project->id]);

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
            'title' => ['required', 'string', 'max:255'],
            'description' => ['sometimes', 'required', 'string'],
            'status' => ['sometimes', 'required', new Enum(TaskStatus::class)],
            'priority' => ['sometimes', 'required', new Enum(TaskPriority::class)],
            'assigned_to' => ['sometimes', 'required', 'integer:strict', 'exists:project_user,user_id'],
            'due_date' => ['sometimes', 'required', 'date', 'date_format:Y-m-d'],
        ];
    }

    public function attributes()
    {
        return [
            'assigned_to' => 'assigned_to',
            'due_date' => 'due_date',
        ];
    }
}
