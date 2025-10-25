<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;

class UserService
{
    /**
     * Create a new class instance.
     */
    public function __construct(private UserRepository $userRepo)
    {
    }

    /**
     * Create the resource.
     *
     * @param array $data
     * @return User
     */
    public function createUser(array $data): User
    {
        return $this->userRepo->create($data);
    }

    /**
     * Find the resource.
     *
     * @param int $id
     * @return User
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     */
    public function getUser(int $id): User
    {
        return $this->userRepo->getById($id);
    }

    /**
     * Find the resource and update it.
     *
     * @param int $id
     * @param array $data
     * @return User
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     */
    public function updateUser(int $id, array $data): User
    {
        $user = $this->userRepo->getById($id);

        return $this->userRepo->update($user, $data);
    }
}
