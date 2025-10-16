<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;

class UserService
{
    private UserRepository $userRepo;

    /**
     * Create a new class instance.
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepo = $userRepository;
    }

    /**
     * Modify the array data to easily save in the table.
     *
     * @param array $data
     * @return User
     */
    public function createUser(array $data): User
    {
        return $this->userRepo->create($data);
    }

    /**
     * Get a user data with requested id.
     *
     * @param int $id
     * @return ?User
     */
    public function getUser(int $id): ?User
    {
        return $this->userRepo->getById($id);
    }

    /**
     * Update a user data with requested id.
     *
     * @param int $id
     * @param array $data
     * @return ?User
     */
    public function updateUser(int $id, array $data): ?User
    {
        return $this->userRepo->update($id, $data);
    }
}
