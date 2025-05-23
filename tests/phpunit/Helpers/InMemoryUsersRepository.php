<?php

declare(strict_types=1);

namespace UnitTests\Helpers;

use App\Domain\Entity\User;
use DateTime;

/**
 * Только для примера in-memory репозитория.
 * В этом проекте нет интерфейсов для репозитория.
 * Идея в том, что вы можете сами сделать тестовый двойник репозитория, котоырый будет иметь тот же контракт, что и реальный репозиторий обращающийся к БД через доктрину, но реализовать упрощенную логику с хранением сущностей в памяти в массиве $storage
 */
class InMemoryUsersRepository implements UserRepositoryInterface
{
    /** @var User[] */
    public array $storage = [];

    /**
     * @param User[]|null $initialUsers
     */
    public function __construct(?array $initialUsers = null)
    {
        if ($initialUsers !== null) {
            foreach ($initialUsers as $user) {
                $this->add($user);
            }
        }
    }

    public function add(User $user): void
    {
        // Ensure user ID is set if it's a new user not yet in storage
        // For a real in-memory repo, you might auto-increment or UUID generate if ID is null.
        // Here, we assume User objects are given IDs before being added, or we just store them.
        $exists = false;
        foreach ($this->storage as $existingUser) {
            if ($existingUser->getId() === $user->getId()) {
                $exists = true;
                break;
            }
        }
        if (!$exists) {
            $this->storage[] = $user;
        }
    }

    public function remove(User $userToRemove): void
    {
        $this->storage = array_filter(
            $this->storage,
            static fn (User $user): bool => $user->getId() !== $userToRemove->getId()
        );
    }

    public function findById(int $userId): ?User
    {
        foreach ($this->storage as $user) {
            if ($user->getId() === $userId && $user->getDeletedAt() === null) {
                return $user;
            }
        }

        return null;
    }

    public function findByLogin(string $login): ?User
    {
        foreach ($this->storage as $user) {
            if ($user->getLogin() === $login && $user->getDeletedAt() === null) {
                return $user;
            }
        }

        return null;
    }

    /**
     * @return User[]
     */
    public function findAll(): array
    {
        return array_filter(
            $this->storage,
            static fn (User $user): bool => $user->getDeletedAt() === null
        );
    }

    // Example of a method that mimics one from App\Infrastructure\Repository\UserRepository
    // This one is simplified for in-memory usage.
    public function create(User $user): int
    {
        // In a real scenario, an in-memory repository might assign an ID.
        // Here we assume the User entity might already have an ID or it's handled externally before add.
        // For simplicity, let's just add it. If it needs an ID, this model assumes it's set.
        
        $idToReturn = $user->getId();
        if ($idToReturn === null) {
            // Attempt to generate a pseudo-ID if none is set, for testing purposes.
            // This is a simplistic approach. A more robust way would be needed for complex scenarios.
            $idToReturn = count($this->storage) + 1; // Not safe for concurrent access or removals.
            // The User entity has a setId method, but it's marked as deprecated.
            // For an in-memory test repository, we might ignore this deprecation or use reflection if strictly needed.
            // However, let's assume for now that the User object is prepared with an ID.
            // If not, the `add` method will store it, and `getId()` will return whatever it has (possibly null).
        }

        $this->add($user);
        return $idToReturn; // Or handle ID generation/assignment more robustly.
    }
} 