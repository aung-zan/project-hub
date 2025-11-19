<?php

namespace App\Http\Requests\ProjectUser;

use App\Enum\ProjectRoles;
use App\Http\Requests\BaseRequest;
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
                $userIds = collect(request()->get('members'))->pluck('id');

                // may or maynot need to change for the frontend.
                $result = $this->checkUsersExist($userIds);
                foreach ($result as $key => $value) {
                    $validator->errors()->add('members.' . $key . '.id', "Resource ID:$value not found.");
                }

                $result = $this->checkUsersExistInProject($userIds);
                foreach ($result as $key => $value) {
                    $validator->errors()->add('members.' . $key . '.id', "ID:$value is already member in the project.");
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
    private function checkUsersExist(Collection $requestUserIds): Collection
    {
        $userIds = $this->userRepo->getByIds($requestUserIds->toArray());

        return $requestUserIds->diff($userIds);
    }

    /**
     * Check which users are already project members.
     *
     * @param Illuminate\Support\Collection $users
     * @return Illuminate\Support\Collection
     */
    private function checkUsersExistInProject(Collection $requestUserIds): Collection
    {
        $memberIds = $this->project->getProjectUserByIds($requestUserIds->toArray());

        return $requestUserIds->intersect($memberIds);
    }
}
