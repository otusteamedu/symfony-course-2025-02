<?php

namespace App\Domain\ValueObject;

use RuntimeException;

// PhpStorm Shortcut для перехода (или создания) к соответствующему тесту: Ctrl + Shift + T
class CommunicationChannel
{
    private const EMAIL = 'email';
    private const PHONE = 'phone';
    private const ALLOWED_VALUES = [self::PHONE, self::EMAIL];

    private function __construct(
        private readonly string $value
    ) {
        if (!in_array($value, self::ALLOWED_VALUES, true)) {
            throw new RuntimeException('Invalid communication channel value');
        }
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
