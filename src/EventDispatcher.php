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

use Psr\EventDispatcher\{
    EventDispatcherInterface,
    ListenerProviderInterface,
    StoppableEventInterface
};

/**
 * EventDispatcher
 *
 * @package Inane\Event
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
