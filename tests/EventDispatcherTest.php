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
 * @author   Philip Michael Raab<philip@cathedral.co.za>
 * @package  inanepain\event
 * @category event
 *
 * @license  UNLICENSE
 * @license  https://unlicense.org/UNLICENSE UNLICENSE
 *
 * _version_ $version
 */

declare(strict_types = 1);

namespace Inane\Event\Tests;

use Inane\Event\{
    Event,
    EventDispatcher,
    LimitedEvent,
    StoppableEvent};
use Inane\Event\Provider\ListenerProvider;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * Tests for `Inane\Event\EventDispatcher`.
 *
 * Verifies listener invocation, event pass through and the honouring of
 * stoppable events.
 */
final class EventDispatcherTest extends TestCase {
    /**
     * Listener Provider
     *
     * @var ListenerProvider
     */
    private ListenerProvider $provider;

    /**
     * Event Dispatcher
     *
     * @var EventDispatcher
     */
    private EventDispatcher $dispatcher;

    /**
     * Creates a fresh provider and dispatcher for each test.
     *
     * @return void
     */
    protected function setUp(): void {
        $this->provider = new ListenerProvider();
        $this->dispatcher = new EventDispatcher($this->provider);
    }

    /**
     * The dispatcher satisfies the PSR-14 contract.
     *
     * @return void
     */
    public function testImplementsEventDispatcherInterface(): void {
        $this->assertInstanceOf(EventDispatcherInterface::class, $this->dispatcher);
    }

    /**
     * The dispatched event instance is returned unchanged.
     *
     * @return void
     */
    public function testDispatchReturnsSameEvent(): void {
        $event = new Event();

        $this->assertSame($event, $this->dispatcher->dispatch($event));
    }

    /**
     * An event without listeners is dispatched without error.
     *
     * @return void
     */
    public function testDispatchWithoutListeners(): void {
        $event = new Event();

        $this->assertSame($event, $this->dispatcher->dispatch($event));
    }

    /**
     * All listeners registered for an event are called in order.
     *
     * @return void
     */
    public function testDispatchCallsAllListenersInOrder(): void {
        $called = [];

        $this->provider->addListener(CustomEvent::class, function(object $event) use (&$called): void {
            $called[] = 'first';
        });
        $this->provider->addListener(CustomEvent::class, function(object $event) use (&$called): void {
            $called[] = 'second';
        });

        $this->dispatcher->dispatch(new CustomEvent());

        $this->assertSame(['first', 'second'], $called);
    }

    /**
     * Only listeners for the dispatched event are called.
     *
     * @return void
     */
    public function testDispatchIgnoresListenersOfOtherEvents(): void {
        $called = [];

        $this->provider->addListener(CustomEvent::class, function(object $event) use (&$called): void {
            $called[] = 'custom';
        });
        $this->provider->addListener(Event::class, function(object $event) use (&$called): void {
            $called[] = 'base';
        });

        $this->dispatcher->dispatch(new Event());

        $this->assertSame(['base'], $called);
    }

    /**
     * Listeners may mutate the event they receive.
     *
     * @return void
     */
    public function testListenerReceivesDispatchedEvent(): void {
        $received = null;
        $event = new CustomEvent();

        $this->provider->addListener($event, function(object $dispatched) use (&$received): void {
            $received = $dispatched;
        });

        $this->dispatcher->dispatch($event);

        $this->assertSame($event, $received);
    }

    /**
     * A listener stopping propagation prevents later listeners running.
     *
     * @return void
     */
    public function testDispatchHonoursStoppedPropagation(): void {
        $called = [];

        $this->provider->addListener(StoppableEvent::class, function(StoppableEvent $event) use (&$called): void {
            $called[] = 'first';
            $event->stopPropagation();
        });
        $this->provider->addListener(StoppableEvent::class, function(StoppableEvent $event) use (&$called): void {
            $called[] = 'second';
        });

        $this->dispatcher->dispatch(new StoppableEvent());

        $this->assertSame(['first'], $called);
    }

    /**
     * A stoppable event already stopped never reaches a listener.
     *
     * @return void
     */
    public function testDispatchSkipsListenersWhenAlreadyStopped(): void {
        $called = 0;

        $this->provider->addListener(StoppableEvent::class, function(StoppableEvent $event) use (&$called): void {
            $called++;
        });

        $event = new StoppableEvent();
        $event->stopPropagation();
        $this->dispatcher->dispatch($event);

        $this->assertSame(0, $called);
    }

    /**
     * A limited event restricts how many listeners are called.
     *
     * @return void
     */
    public function testDispatchRespectsEventLimit(): void {
        $called = 0;
        $listener = function(LimitedEvent $event) use (&$called): void {
            $called++;
        };

        $this->provider->addListener(LimitedEvent::class, $listener);
        $this->provider->addListener(LimitedEvent::class, function(LimitedEvent $event) use (&$called): void {
            $called++;
        });
        $this->provider->addListener(LimitedEvent::class, function(LimitedEvent $event) use (&$called): void {
            $called++;
        });

        $this->dispatcher->dispatch(new LimitedEvent(2));

        $this->assertSame(2, $called);
    }
}
