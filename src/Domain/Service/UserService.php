<?php

namespace App\Domain\Service;

use App\Domain\Entity\User;
use App\Infrastructure\Repository\UserRepository;

class UserService
{
    public function __construct(private readonly UserRepository $userRepository)
    {
    }

    public function create(string $login): User
    {
        $user = new User();
        $user->setLogin($login);
        $user->setCreatedAt();
        $user->setUpdatedAt();
        $this->userRepository->create($user);

        return $user;
    }

    public function refresh(User $user): void
    {
        $this->userRepository->refresh($user);
    }

    public function subscribeUser(User $author, User $follower): void
    {
        $this->userRepository->subscribeUser($author, $follower);
    }

    /**
     * @return User[]
     */
    public function findUsersByLogin(string $login): array
    {
        return $this->userRepository->findUsersByLogin($login);
    }

    /**
     * @return User[]
     */
    public function findUsersByLoginWithCriteria(string $login): array
    {
        return $this->userRepository->findUsersByLoginWithCriteria($login);
    }
}
