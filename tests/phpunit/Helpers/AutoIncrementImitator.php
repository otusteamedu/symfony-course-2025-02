<?php

declare(strict_types=1);

namespace UnitTests\Helpers;

use Closure;
use InvalidArgumentException;
use ReflectionObject;

/**
 * Imitates auto-increment behaviour of persistence layer.
 * Updates private id attribute of an entity with given value.
 * Usage:
 *          $recordRepoMock->expects(self::once())
 *               ->method('add')
 *               ->willReturnCallback(
 *                  AutoIncrementImitator::updatePrivateEntityIdCallback(1)
 *               );
 */
class AutoIncrementImitator
{
    /**
     * @template T
     *
     * @return Closure(T): T
     */
    public static function updatePrivateEntityIdCallback(?int $returnId): callable
    {
        return static function ($entity) use ($returnId) {
            if (!is_object($entity)) {
                throw new InvalidArgumentException('Provided entity is not an object');
            }
            $reflection = new ReflectionObject($entity);
            $property   = $reflection->getProperty('id');
            $property->setAccessible(true);
            $property->setValue($entity, $returnId ?? UniqueIncrementIdFactory::next());

            return $entity;
        };
    }
}
