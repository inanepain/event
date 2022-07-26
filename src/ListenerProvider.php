<?php

/**
 * Inane
 *
 * Event
 *
 * PHP version 8.1
 *
 * @package Inane\Event
 * @author Philip Michael Raab<peep@inane.co.za>
 *
 * @license UNLICENSE
 * @license https://github.com/inanepain/event/raw/develop/UNLICENSE UNLICENSE
 *
 * @version $Id$
 * $Date$
 */

declare(strict_types=1);

namespace Inane\Event;

use Psr\EventDispatcher\ListenerProviderInterface;

use function array_key_exists;
use function get_class;

/**
 * Class ListenerProvider
 *
 * @package Inane\Event
 *
 * @version 1.0.0
 */
class ListenerProvider implements ListenerProviderInterface {

    /**
     * Listeners
     *
     * @var array
     */
    private array $listeners = [];

    /**
     * Get listeners for $event
     *
     * @param object $event
     *   An event for which to return the relevant listeners.
     *
     * @return iterable[callable]
     *   An iterable (array, iterator, or generator) of callables.  Each
     *   callable MUST be type-compatible with $event.
     */
    public function getListenersForEvent(object $event): iterable {
        $eventType = get_class($event);

        if (array_key_exists($eventType, $this->listeners)) return $this->listeners[$eventType];

        return [];
    }

    /**
     * Add Listener
     *
     * @param string $eventType
     * @param callable $callable
     *
     * @return $this
     */
    public function addListener(string $eventType, callable $callable): self {
        $this->listeners[$eventType][] = $callable;
        return $this;
    }

    /**
     * Clear listeners for $eventType
     *
     * @param string $eventType
     */
    public function clearListeners(string $eventType): void {
        if (array_key_exists($eventType, $this->listeners)) unset($this->listeners[$eventType]);
    }
}
