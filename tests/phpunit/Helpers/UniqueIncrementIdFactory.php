<?php

declare(strict_types=1);

namespace UnitTests\Helpers;

class UniqueIncrementIdFactory
{
    private static int $lastUsedIncrementId = 0;

    public static function next(): int
    {
        return ++self::$lastUsedIncrementId;
    }
}
