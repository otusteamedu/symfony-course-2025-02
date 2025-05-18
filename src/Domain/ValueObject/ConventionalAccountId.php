<?php


declare(strict_types=1);

namespace App\Domain\ValueObject;


use Webmozart\Assert\Assert;

/**
 * Пример Value Object. Не используется в коде.
 *
 * Моделирует специфичны для бизнеса формат идентификатора:
 * <resellerId>-<platformId>-<userId>-<userType>
 * Пример: 1174-2222-123-customer
 */
final class ConventionalAccountId
{
    private const ID_PATTERN = '/^\d{1,4}-\d{1,4}-\d{1,6}-(customer|creator)$/';

    private ?int $resellerId    = null;
    private ?int $platformId     = null;
    private ?int $userId        = null;
    private ?string $typeOfUser = null;

    public function __construct(
        protected string $value,
    ) {
        Assert::regex($value, self::ID_PATTERN, 'Invalid identifier format.');
    }

    public function isEqual(self $another): bool
    {
        return $this->value === $another->value;
    }

    public function getResellerId(): int
    {
        if ($this->resellerId === null) {
            $this->breakDownAndCache();
        }

        return $this->resellerId;
    }

    public function getPlatformId(): int
    {
        if ($this->platformId === null) {
            $this->breakDownAndCache();
        }

        return $this->platformId;
    }

    public function getUserId(): int
    {
        if ($this->userId === null) {
            $this->breakDownAndCache();
        }

        return $this->userId;
    }

    public function isCustomer(): bool
    {
        if ($this->typeOfUser === null) {
            $this->breakDownAndCache();
        }

        return $this->typeOfUser === 'customer';
    }

    public function isCreator(): bool
    {
        if ($this->typeOfUser === null) {
            $this->breakDownAndCache();
        }

        return $this->typeOfUser === 'creator';
    }

    public function toString(): string
    {
        return $this->value;
    }

    private function breakDownAndCache(): void
    {
        $parts            = explode('-', $this->value);
        $this->resellerId = (int) $parts[0];
        $this->platformId  = (int) $parts[1];
        $this->userId     = (int) $parts[2];
        $this->typeOfUser = $parts[3];
    }
}
