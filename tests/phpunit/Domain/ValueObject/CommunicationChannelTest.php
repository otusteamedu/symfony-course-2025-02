<?php

declare(strict_types=1);

namespace Domain\ValueObject;

use App\Domain\ValueObject\CommunicationChannel;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RuntimeException;

#[CoversClass(CommunicationChannel::class)]
final class CommunicationChannelTest extends TestCase
{
    #[Test]
    #[DataProvider('validChannelsProvider')]
    public function it_creates_valid_channel(string $value): void
    {
        $channel = CommunicationChannel::fromString($value);
        
        $this->assertSame($value, $channel->getValue());
    }

    #[Test]
    #[DataProvider('invalidChannelsProvider')]
    public function it_throws_exception_for_invalid_channel(string $invalidValue): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Invalid communication channel value');

        CommunicationChannel::fromString($invalidValue);
    }

    public static function validChannelsProvider(): array
    {
        return [
            ['email'],
            ['phone'],
        ];
    }

    public static function invalidChannelsProvider(): array
    {
        return [
            'empty string' => [''],
            'whitespace only' => ['   '],
            'mixed case email' => ['EMAIL'],
            'mixed case phone' => ['PHONE'],
            'email with spaces' => ['email '],
            'phone with special chars' => ['phone@'],
            'email with numbers' => ['email123'],
            'phone with letters' => ['phoneabc'],
            'null string' => ["\0"],
            'special chars only' => ['!@#$%^&*()'],
            'unicode chars' => ['📱'],
            'very long string' => [str_repeat('a', 1000)],
            'mixed valid values' => ['emailphone'],
            'html tags' => ['<script>alert("email")</script>'],
            'sql injection attempt' => ["email' OR '1'='1"],
        ];
    }
}