<?php

/**
 * Inane: Event
 *
 * PSR-14 implementation: event dispatcher.
 *
 * $Id$
 * $Date$
 *
 * PHP version 8.4
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
    StoppableEventInterface
};

/**
 * EventDispatcher
 *
 * @version 1.0.0
 */
class EventDispatcher implements EventDispatcherInterface {
    /**
     * EventDispatcher constructor
     *
     * @param ListenerProviderInterface $provider
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
     * dispatch
     *
     * @param object $event
     *
     * @return object
     */
    public function dispatch(object $event): object {
        if ($event instanceof StoppableEventInterface && $event->isPropagationStopped()) return $event;

        foreach ($this->provider->getListenersForEvent($event) as $listener) {
            $listener($event);
            if ($event instanceof StoppableEventInterface && $event->isPropagationStopped()) break;
        }

        return $event;
    }
}
