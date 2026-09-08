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

use InvalidArgumentException;

use function in_array;
use function is_object;

/**
 * ListenerProviderTrait
 * Listener registry keyed on event name, shared by the listener providers.
 * Listeners are returned in the order they were added.
 *
 * @version 1.0.0
 */
trait ListenerProviderTrait {
    #region Provider
    /**
     * Event Listeners
     * Lazily initialised store of listeners, keyed on event name.
     *
     * @var array<string, list<callable>>
     */
    protected array $eventListeners = [];

    /**
     * Get Event Name
     * Resolves an event instance or class name to the key used in the registry.
     *
     * @param string|object $event Event class name or instance.
     *
     * @return string Event name.
     */
    protected static function getEventName(string|object $event): string {
        return is_object($event) ? $event::class : $event;
    }

    /**
     * Add Listener
     * Registers a listener for an event, ignoring exact duplicates.
     *
     * @param string|object $event    Event class name or instance.
     * @param callable      $listener Listener callable.
     *
     * @return static The called object.
     *
     * @throws InvalidArgumentException
     */
    public function addListener(string|object $event, callable $listener): static {
        $event = static::getEventName($event);
        // Seed an empty listener list the first time the event is seen.
        if (!isset($this->eventListeners[$event])) $this->eventListeners[$event] = [];

        // Strict comparison so the same callable is not registered twice.
        if (!in_array($listener, $this->eventListeners[$event], true)) $this->eventListeners[$event][] = $listener;

        return $this;
    }

    /**
     * Get Listeners For Event
     * Returns the listeners registered for an event, in registration order.
     *
     * @param string|object $event Event class name or instance.
     *
     * @return iterable Listeners for the event, empty when none are registered.
     *
     * @throws InvalidArgumentException
     */
    public function getListenersForEvent(string|object $event): iterable {
        $event = static::getEventName($event);
        if (isset($this->eventListeners[$event])) return $this->eventListeners[$event];

        return [];
    }

    /**
     * Clear Listeners
     * Removes all listeners registered for an event.
     *
     * @param string|object $event Event class name or instance.
     *
     * @return void
     *
     */
    public function clearListeners(string|object $event): void {
        $event = static::getEventName($event);
        unset($this->eventListeners[$event]);
    }
    #endregion Provider
}
