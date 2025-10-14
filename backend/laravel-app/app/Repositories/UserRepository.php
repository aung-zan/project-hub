<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    private $user;

    /**
     * Create a new class instance.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Save the data in the user table.
     *
     * @param array $data
     * @return User
     */
    public function createUser(array $data): User
    {
        return $this->user->create($data);
    }
}
