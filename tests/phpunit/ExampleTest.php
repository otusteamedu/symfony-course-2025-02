<?php

declare(strict_types=1);

namespace UnitTests;

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Базовый пример phpunit теста
 * - использует вариант нейминга тестов с префиксом test, а так же с помощью аннотаций
 * - CoversNothing указывет что этот класс не используется для вычисления покрытия никаких классов нашего кода
 */
#[CoversNothing]
final class ExampleTest extends TestCase
{
    #[Test]
    public function it_can_perform_basic_assertions(): void
    {
        self::assertTrue(true);
        self::assertEquals(2, 1 + 1);
    }

    public function testStringOperations(): void
    {
        $string = 'Hello World';
        
        self::assertStringContainsString('World', $string);
        self::assertStringStartsWith('Hello', $string);
    }
}