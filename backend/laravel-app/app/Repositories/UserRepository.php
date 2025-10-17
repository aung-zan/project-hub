<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    private User $user;

    /**
     * Create a new class instance.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
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
     * Find a resource with requested id and update it.
     * if an id is not found, throws exception.
     *
     * @param int $id
     * @param array $data
     * @return ?User
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function update(int $id, array $data): ?User
    {
        $user = $this->getById($id);

        $user->update($data);

        return $user;
    }
}
