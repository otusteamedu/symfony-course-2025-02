<?php

declare(strict_types=1);

namespace UnitTests\Helpers;

/**
 * Пример
 */
class EventDispatcherSpy implements EventDispatcherInterface
{
    /** @var DomainEventInterface[] */
    public array $recordedEvents = [];

    public function __construct(
        private ?EventDispatcherInterface $realEventDispatcher = null
    ) {
    }

    public function dispatch(DomainEventInterface ...$events): void
    {
        foreach ($events as $event) {
            $this->recordedEvents[] = $event;

            if ($this->realEventDispatcher !== null) {
                $this->realEventDispatcher->dispatch($event);
            }
        }
    }
}
