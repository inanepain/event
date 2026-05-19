<?php

/**
 * Inane: Event
 * PSR-14 implementation: event dispatcher.
 * $Id$
 * $Date$
 * PHP version 8.5
 *
 * @author   Philip Michael Raab<philip@cathedral.co.za>
 * @package  inanepain\event
 * @category event
 * @license  UNLICENSE
 * @license  https://unlicense.org/UNLICENSE UNLICENSE
 * _version_ $version
 */

declare(strict_types = 1);

namespace Inane\Event\Provider;

use Psr\EventDispatcher\ListenerProviderInterface;

use function is_object;
use function krsort;

use const SORT_NUMERIC;

/**
 * PrioritisedListenerProvider
 * A listener provider that returns listeners ordered by priority (highest first).
 * Listeners with the same priority are returned in the order they were added.
 *
 * @version 1.0.0
 */
class PrioritisedListenerProvider implements ListenerProviderInterface {
    /**
     * Listeners indexed by event name and priority
     *
     * @var array<string, array<int, callable[]>>
     */
    protected array $listeners = [];

    /**
     * Get the event name from a string or object
     *
     * @param string|object $event
     *
     * @return string
     */
    protected static function getEventName(string|object $event): string {
        return is_object($event) ? $event::class : $event;
    }

    /**
     * Add a listener for an event with an optional priority.
     * Higher priority values are called first. Default priority is 0.
     *
     * @param string|object $event    The event class name or instance.
     * @param callable      $listener The listener callable.
     * @param int           $priority Priority (higher = called first).
     *
     * @return static
     */
    public function addListener(string|object $event, callable $listener, int $priority = 0): static {
        $name = static::getEventName($event);
        $this->listeners[$name][$priority][] = $listener;

        return $this;
    }

    /**
     * Get Listeners For Event
     * Returns listeners ordered by priority (highest first).
     *
     * @param object $event
     *
     * @return iterable
     */
    public function getListenersForEvent(object $event): iterable {
        $name = static::getEventName($event);

        if (!isset($this->listeners[$name])) return [];

        $priorityGroups = $this->listeners[$name];
        krsort($priorityGroups, SORT_NUMERIC);

        foreach($priorityGroups as $listeners) {
            yield from $listeners;
        }
    }

    /**
     * Remove all listeners for an event.
     *
     * @param string|object $event
     *
     * @return void
     */
    public function clearListeners(string|object $event): void {
        $name = static::getEventName($event);
        unset($this->listeners[$name]);
    }
}
