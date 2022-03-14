<?php

namespace Inane\Event;

use Psr\EventDispatcher\{
    EventDispatcherInterface,
    ListenerProviderInterface,
    StoppableEventInterface
};

/**
 * Class EventDispatcher
 */
class EventDispatcher implements EventDispatcherInterface {
    /**
     * @var ListenerProviderInterface
     */
    private $listenerProvider;

    /**
     * EventDispatcher constructor.
     * @param ListenerProviderInterface $listenerProvider
     */
    public function __construct(ListenerProviderInterface $listenerProvider) {
        $this->listenerProvider = $listenerProvider;
    }

    /**
     * @param object $event
     * @return object
     */
    public function dispatch(object $event): object {

        if ($event instanceof StoppableEventInterface && $event->isPropagationStopped()) {
            return $event;
        }
        foreach ($this->listenerProvider->getListenersForEvent($event) as $listener) {
            $listener($event);
        }
        return $event;
    }
}
