<?php

namespace App\Http\Requests\Task;

use App\Http\Requests\BaseRequest;

class TaskUpdateRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'required', 'string'],
            // need enum TaskStatus
            'status' => ['sometimes', 'required'],
            'priority' => ['sometimes', 'required', new Enum(TaskPriority::class)],
            'assigned_to' => ['sometimes', 'required', 'integer:strict', 'exists:project_user,user_id'],
            'due_date' => ['sometimes', 'required', 'date', 'date_format:Y-m-d'],
        ];
    }
}
