<?php

namespace App\Http\Requests\Project;

use App\Enum\ProjectStatus;
use App\Http\Requests\BaseRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rules\Enum;

class ProjectIndexRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'search' => ['sometimes', 'required', 'string'],
            'status' => ['sometimes', 'required', new Enum(ProjectStatus::class)],
            'start' => ['sometimes', 'required', 'date', 'date_format:Y-m-d'],
            'end' => ['sometimes', 'required', 'date', 'date_format:Y-m-d', 'after:start'],
            'sort' => ['sometimes', 'required', 'string'],
        ];
    }

    /**
     * Get the "after" validation callables for the request.
     */
    public function after(): array
    {
        $acceptColumns = ['id', 'name', 'start', 'end'];
        $acceptDirections = ['asc', 'desc'];

        return [
            function (Validator $validator) use ($acceptColumns, $acceptDirections) {
                $sort = request()->get('sort', []);

                if (!empty($sort)) {
                    list($column, $direction) = explode('_', $sort);

                    if (empty($column) || empty($direction)) {
                        return $validator->errors()->add(
                            'sort',
                            'The sort field must be match with the format column_direction.'
                        );
                    }

                    if (!in_array($column, $acceptColumns)) {
                        $validator->errors()->add('sort', 'The column of sort field is invalid.');
                    }

                    if (!in_array($direction, $acceptDirections)) {
                        $validator->errors()->add('sort', 'The direction of sort field is invalid.');
                    }
                }
            }
        ];
    }
}
