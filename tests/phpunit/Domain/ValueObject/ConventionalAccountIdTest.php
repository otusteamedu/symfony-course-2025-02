<?php

declare(strict_types=1);

namespace UnitTests\Domain\ValueObject;

use App\Domain\ValueObject\ConventionalAccountId;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(ConventionalAccountId::class)]
class ConventionalAccountIdTest extends TestCase
{
    #[DataProvider('validRecipientIdProvider')]
    public function testCreatingValid(string $validId): void
    {
        $recipientId = new ConventionalAccountId($validId);
        $this->assertInstanceOf(ConventionalAccountId::class, $recipientId);
    }

    #[DataProvider('invalidRecipientIdProvider')]
    public function testThrowsExceptionForInvalidIdFormat(string $invalidId): void
    {
        $this->expectException(InvalidArgumentException::class);
        new ConventionalAccountId($invalidId);
    }

    public function testChecksEqualityOfTwoRecipientIds(): void
    {
        $id1 = new ConventionalAccountId('1234-5678-9101-customer');
        $id2 = new ConventionalAccountId('1234-5678-9101-customer');
        $id3 = new ConventionalAccountId('4321-8765-1019-creator');

        $this->assertTrue($id1->isEqual($id2));
        $this->assertFalse($id1->isEqual($id3));
    }

    public function testExtractsResellerId(): void
    {
        $recipientId = new ConventionalAccountId('1234-5678-9101-customer');
        $this->assertEquals(1234, $recipientId->getResellerId());
    }

    public function testExtractsPlatformId(): void
    {
        $recipientId = new ConventionalAccountId('1234-5678-9101-customer');
        $this->assertEquals(5678, $recipientId->getPlatformId());
    }

    public function testExtractsUserId(): void
    {
        $recipientId = new ConventionalAccountId('1234-5678-9101-customer');
        $this->assertEquals(9101, $recipientId->getUserId());
    }

    public function testDeterminesIfUserIsCustomer(): void
    {
        $recipientId = new ConventionalAccountId('1234-5678-9101-customer');
        $this->assertTrue($recipientId->isCustomer());
        $this->assertFalse($recipientId->isCreator());
    }

    public function testDeterminesIfUserIsCreator(): void
    {
        $recipientId = new ConventionalAccountId('1234-5678-9101-creator');
        $this->assertTrue($recipientId->isCreator());
        $this->assertFalse($recipientId->isCustomer());
    }

    public static function invalidRecipientIdProvider(): array
    {
        return [
            // Invalid because it is an incomplete ID
            'empty id' => [''],
            'incomplete 1' => ['1234'],
            'incomplete 2' => ['1234-5678'],
            'incomplete no postfix' => ['1234-5678-9101'],

            // Invalid because it contains alphanumeric characters in IDs
            'alphanumeric in ids 1' => ['abcd-5678-9101-customer'],
            'alphanumeric in ids 2' => ['5678-abcd-9101-customer'],
            'alphanumeric in ids 3' => ['5678-5678-abcd-customer'],

            // Invalid because the postfix is incorrect
            'incorrect-postfix' => ['1234-5678-9101-unknown'],

            // Invalid as it contains fewer sections than required
            ['1234-5678-customer'],
            // Invalid due to an excessive length of digits in segments
            ['12345-1-1-customer'],
            ['1-12345-1-customer'],
            ['1-1-1234567-customer'],
        ];
    }

    public static function validRecipientIdProvider(): array
    {
        return [
            // Min digits in segments
            ['1-2-3-customer'],
            // Max digits in segments
            ['1234-1234-123456-customer'],
            ['1234-1234-123456-creator'],
            // Other roles
            ['1-1-1-creator'],
        ];
    }
}
