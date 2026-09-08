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

namespace Inane\Event\Tests\Provider;

use Inane\Event\Attribute\Listener;
use Inane\Event\Event;
use Inane\Event\EventDispatcher;
use Inane\Event\Provider\ListenerProvider;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\ListenerProviderInterface;

/**
 * Tests for `Inane\Event\Provider\ListenerProvider`.
 *
 * Verifies listener registration, retrieval, duplicate prevention and
 * clearing of listeners.
 */
final class ListenerProviderTest extends TestCase {
    /**
     * Listener Provider
     *
     * @var ListenerProvider
     */
    private ListenerProvider $provider;

    /**
     * Creates a fresh provider for each test.
     *
     * @return void
     */
    protected function setUp(): void {
        $this->provider = new ListenerProvider();
    }

    /**
     * The provider satisfies the PSR-14 contract.
     *
     * @return void
     */
    public function testImplementsListenerProviderInterface(): void {
        $this->assertInstanceOf(ListenerProviderInterface::class, $this->provider);
    }

    /**
     * An unknown event returns no listeners.
     *
     * @return void
     */
    public function testUnknownEventReturnsEmptyList(): void {
        $this->assertSame([], [...$this->provider->getListenersForEvent(new Event())]);
    }

    /**
     * `addListener()` is fluent.
     *
     * @return void
     */
    public function testAddListenerIsFluent(): void {
        $result = $this->provider->addListener(Event::class, fn(object $event): null => null);

        $this->assertSame($this->provider, $result);
    }

    /**
     * Attributed public methods are registered and dispatched on the supplied object.
     *
     * @return void
     */
    public function testAttributedListenerIsFluentAndDispatched(): void {
        $listener = new AttributedListenerStub();

        $this->assertSame($this->provider, $this->provider->addAttributedListener($listener));

        (new EventDispatcher($this->provider))->dispatch(new Event());

        $this->assertSame([Event::class], $listener->events);
    }

    /**
     * A repeatable attribute registers the method for every declared event.
     *
     * @return void
     */
    public function testRepeatableAttributedListenerRegistersEveryEvent(): void {
        $listener = new AttributedListenerStub();
        $this->provider->addAttributedListener($listener);

        (new EventDispatcher($this->provider))->dispatch(new EventProviderStub());

        $this->assertSame([EventProviderStub::class], $listener->events);
    }

    /**
     * Attributed registrations preserve method declaration order.
     *
     * @return void
     */
    public function testAttributedListenersPreserveRegistrationOrder(): void {
        $listener = new OrderedAttributedListenerStub();
        $this->provider->addAttributedListener($listener);

        (new EventDispatcher($this->provider))->dispatch(new Event());

        $this->assertSame(['first', 'second'], $listener->calls);
    }

    /**
     * Unannotated methods are not registered.
     *
     * @return void
     */
    public function testUnannotatedMethodsAreIgnored(): void {
        $this->provider->addAttributedListener(new UnannotatedListenerStub());

        $this->assertSame([], [...$this->provider->getListenersForEvent(new Event())]);
    }

    /**
     * Invalid attributed declarations are rejected.
     *
     * @return void
     */
    public function testInvalidAttributedDeclarationsAreRejected(): void {
        $this->expectException(InvalidArgumentException::class);

        $this->provider->addAttributedListener(new InvalidEventAttributedListenerStub());
    }

    /**
     * Non-public attributed methods are rejected.
     *
     * @return void
     */
    public function testNonPublicAttributedMethodsAreRejected(): void {
        $this->expectException(InvalidArgumentException::class);

        $this->provider->addAttributedListener(new NonPublicAttributedListenerStub());
    }

    /**
     * Listeners registered by class name are returned for an instance.
     *
     * @return void
     */
    public function testListenersRegisteredByClassName(): void {
        $listener = fn(object $event): null => null;
        $this->provider->addListener(Event::class, $listener);

        $listeners = [...$this->provider->getListenersForEvent(new Event())];

        $this->assertCount(1, $listeners);
        $this->assertSame($listener, $listeners[0]);
    }

    /**
     * Listeners may be registered using an event instance.
     *
     * @return void
     */
    public function testListenersRegisteredByInstance(): void {
        $listener = fn(object $event): null => null;
        $this->provider->addListener(new Event(), $listener);

        $listeners = [...$this->provider->getListenersForEvent(new Event())];

        $this->assertCount(1, $listeners);
    }

    /**
     * Listeners are returned in registration order.
     *
     * @return void
     */
    public function testListenersReturnedInRegistrationOrder(): void {
        $first = fn(object $event): string => 'first';
        $second = fn(object $event): string => 'second';

        $this->provider->addListener(Event::class, $first)
            ->addListener(Event::class, $second);

        $listeners = [...$this->provider->getListenersForEvent(new Event())];

        $this->assertSame([$first, $second], $listeners);
    }

    /**
     * The same listener is only registered once per event.
     *
     * @return void
     */
    public function testDuplicateListenerIsNotAddedTwice(): void {
        $listener = fn(object $event): null => null;

        $this->provider->addListener(Event::class, $listener)
            ->addListener(Event::class, $listener);

        $this->assertCount(1, [...$this->provider->getListenersForEvent(new Event())]);
    }

    /**
     * Listeners are kept separate per event.
     *
     * @return void
     */
    public function testListenersAreScopedToEvent(): void {
        $this->provider->addListener(Event::class, fn(object $event): null => null);

        $this->assertCount(1, [...$this->provider->getListenersForEvent(new Event())]);
        $this->assertSame([], [...$this->provider->getListenersForEvent(new EventProviderStub())]);
    }

    /**
     * `clearListeners()` removes all listeners for an event.
     *
     * @return void
     */
    public function testClearListeners(): void {
        $this->provider->addListener(Event::class, fn(object $event): null => null);
        $this->provider->clearListeners(Event::class);

        $this->assertSame([], [...$this->provider->getListenersForEvent(new Event())]);
    }

    /**
     * Clearing an unknown event is harmless.
     *
     * @return void
     */
    public function testClearListenersForUnknownEvent(): void {
        $this->provider->clearListeners(Event::class);

        $this->assertSame([], [...$this->provider->getListenersForEvent(new Event())]);
    }
}

/**
 * A distinct event class used to verify listener scoping.
 */
class EventProviderStub extends Event {}

final class AttributedListenerStub {
    /** @var list<class-string<Event>> */
    public array $events = [];

    #[Listener(Event::class)]
    #[Listener(EventProviderStub::class)]
    public function handle(Event $event): void {
        $this->events[] = $event::class;
    }
}

final class OrderedAttributedListenerStub {
    /** @var list<string> */
    public array $calls = [];

    #[Listener(Event::class, priority: -10)]
    public function first(Event $event): void {
        $this->calls[] = 'first';
    }

    #[Listener(Event::class, priority: 10)]
    public function second(Event $event): void {
        $this->calls[] = 'second';
    }
}

final class UnannotatedListenerStub {
    public function handle(Event $event): void {}
}

final class InvalidEventAttributedListenerStub {
    #[Listener('Inane\\Event\\Tests\\Provider\\MissingEvent')]
    public function handle(Event $event): void {}
}

final class NonPublicAttributedListenerStub {
    #[Listener(Event::class)]
    private function handle(Event $event): void {}
}
