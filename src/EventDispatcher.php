<?php

/**
 * Inane: Event
 *
 * PSR-14 implementation: event dispatcher.
 *
 * $Id$
 * $Date$
 *
 * PHP version 8.5
 *
 * @author Philip Michael Raab<philip@cathedral.co.za>
 * @package inanepain\event
 * @category event
 *
 * @license UNLICENSE
 * @license https://unlicense.org/UNLICENSE UNLICENSE
 *
 * _version_ $version
 */

declare(strict_types=1);

namespace Inane\Event;

use Psr\EventDispatcher\{
    EventDispatcherInterface,
    ListenerProviderInterface,
    StoppableEventInterface};

/**
 * EventDispatcher
 *
 * PSR-14 dispatcher: hands an event to each listener supplied by the provider,
 * honouring stoppable events.
 *
 * @version 1.0.0
 */
class EventDispatcher implements EventDispatcherInterface {
    /**
     * EventDispatcher constructor
     *
     * @param ListenerProviderInterface $provider Source of the listeners for a dispatched event.
     */
    public function __construct(
        /**
         * Listener Provider
         *
         * @var ListenerProviderInterface
         */
        protected ListenerProviderInterface $provider
    ) {
    }

    /**
     * Dispatch
     *
     * Calls every listener for the event, stopping early once a stoppable event
     * has had its propagation stopped.
     *
     * @param object $event The event to dispatch.
     *
     * @return object The event, possibly mutated by the listeners.
     */
    public function dispatch(object $event): object {
        foreach ($this->provider->getListenersForEvent($event) as $listener) {
            // Checked before each call so a listener can stop the ones that follow.
            if ($event instanceof StoppableEventInterface && $event->isPropagationStopped()) return $event;
            $listener($event);
        }

        return $event;
    }
}
