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
 * @copyright 2022 Philip Michael Raab <peep@inane.co.za>
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
     * Listener Provider
     *
     * @var ListenerProviderInterface
     */
    private ListenerProviderInterface $listenerProvider;

    /**
     * EventDispatcher constructor.
     *
     * @param ListenerProviderInterface $listenerProvider
     */
    public function __construct(ListenerProviderInterface $listenerProvider) {
        $this->listenerProvider = $listenerProvider;
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

        foreach ($this->listenerProvider->getListenersForEvent($event) as $listener) $listener($event);

        return $event;
    }
}
