<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    /**
     * Create a new class instance.
     */
    public function __construct(private User $user)
    {
    }

    /**
     * Create a resource in the user table.
     *
     * @param array $data
     * @return User
     */
    public function create(array $data): User
    {
        return $this->user->create($data);
    }

    /**
     * Find a resource with requested id.
     * if an id is not found, throws exception.
     *
     * @param int $id
     * @return ?User
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getById(int $id): ?User
    {
        return $this->user->findOrFail($id);
    }

    /**
     * Update the resource.
     *
     * @param User $user
     * @param array $data
     * @return User
     */
    public function update(User $user, array $data): User
    {
        $user->update($data);

        return $user;
    }
}
