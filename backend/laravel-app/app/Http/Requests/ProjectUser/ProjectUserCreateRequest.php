<?php

namespace App\Http\Requests\ProjectUser;

use App\Enum\ProjectRoles;
use App\Http\Requests\BaseRequest;
use App\Models\Project;
use App\Repositories\ProjectUserRepository;
use App\Repositories\UserRepository;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rules\Enum;

class ProjectUserCreateRequest extends BaseRequest
{
    public function __construct(
        private UserRepository $userRepo
    ) {
    }

    public function authorize(): bool
    {
        Gate::authorize('view', $this->project);

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
            'members' => ['required', 'array'],
            'members.*' => ['required', 'array'],
            'members.*.id' => ['required', 'integer:strict', 'min:1'],
            'members.*.role' => ['required', new Enum(ProjectRoles::class)],
        ];
    }

    public function attributes(): array
    {
        return [
            'members' => 'members',
            'members.*' => 'member',
            'members.*.id' => 'member id',
            'members.*.role' => 'member role',
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                $users = collect(request()->get('members'))->pluck('id');

                // may or maynot need to change for the frontend.
                $result = $this->checkUsersExist($users);
                foreach ($result as $key => $value) {
                    $validator->errors()->add('members.' . $key . '.id', 'Resource not found.');
                }
            }
        ];
    }

    /**
     * Check which users are not exists in table.
     *
     * @param Illuminate\Support\Collection $users
     * @return Illuminate\Support\Collection
     */
    private function checkUsersExist(Collection $users)
    {
        $storedUsers = $this->userRepo->getAllUserIds();

        return $users->diff($storedUsers);
    }
}
