<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Collection;

class UserRepository
{
    /**
     * Create a new class instance.
     */
    public function __construct(private User $user)
    {
    }

    /**
     * Create a resource in a table.
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
     * @return User
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getById(int $id): User
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

    /**
     * Get all users' ids from users table.
     *
     * @return Illuminate\Support\Collection
     */
    public function getAllUserIds(): Collection
    {
        return $this->user->pluck('id');
    }
}
