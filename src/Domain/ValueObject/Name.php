<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use Webmozart\Assert\Assert;

/**
 * Пример Value Object моделирующего имя человека.
 * Не используется в коде. Для лекции по unit тестированию
 */
final class Name
{
    private string $first;
    private string $last;

    public function __construct(string $first, string $last)
    {
        Assert::stringNotEmpty($first);
        Assert::stringNotEmpty($last);

        $this->first = $first;
        $this->last  = $last;
    }

    public function getFirst(): string
    {
        return $this->first;
    }

    public function getLast(): string
    {
        return $this->last;
    }

    public function getFull(): string
    {
        return $this->first . ' ' . $this->last;
    }

    public function isEqual(self $anotherName): bool
    {
        return ($this->first === $anotherName->getFirst())
            && ($this->last === $anotherName->getLast());
    }

    public function __toString(): string
    {
        return $this->getFull();
    }
}
