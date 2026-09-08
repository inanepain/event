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
use Inane\Event\Provider\PrioritisedListenerProvider;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\ListenerProviderInterface;

/**
 * Tests for `Inane\Event\Provider\PrioritisedListenerProvider`.
 *
 * Verifies that listeners are returned highest priority first, that equal
 * priorities preserve registration order and that listeners can be cleared.
 */
final class PrioritisedListenerProviderTest extends TestCase {
    /**
     * Prioritised Listener Provider
     *
     * @var PrioritisedListenerProvider
     */
    private PrioritisedListenerProvider $provider;

    /**
     * Creates a fresh provider for each test.
     *
     * @return void
     */
    protected function setUp(): void {
        $this->provider = new PrioritisedListenerProvider();
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
     * Attributed listeners are ordered by their declared priority.
     *
     * @return void
     */
    public function testAttributedListenersAreOrderedByPriority(): void {
        $listener = new PrioritisedAttributedListenerStub();
        $this->provider->addAttributedListener($listener);

        (new EventDispatcher($this->provider))->dispatch(new Event());

        $this->assertSame(['high', 'first', 'second', 'low'], $listener->calls);
    }

    /**
     * Higher priority listeners are returned first.
     *
     * @return void
     */
    public function testListenersOrderedByPriority(): void {
        $low = fn(object $event): string => 'low';
        $normal = fn(object $event): string => 'normal';
        $high = fn(object $event): string => 'high';

        $this->provider->addListener(Event::class, $low, -10)
            ->addListener(Event::class, $normal)
            ->addListener(Event::class, $high, 10);

        $listeners = [...$this->provider->getListenersForEvent(new Event())];

        $this->assertSame([$high, $normal, $low], $listeners);
    }

    /**
     * Listeners of equal priority keep their registration order.
     *
     * @return void
     */
    public function testEqualPriorityKeepsRegistrationOrder(): void {
        $first = fn(object $event): string => 'first';
        $second = fn(object $event): string => 'second';

        $this->provider->addListener(Event::class, $first, 5)
            ->addListener(Event::class, $second, 5);

        $listeners = [...$this->provider->getListenersForEvent(new Event())];

        $this->assertSame([$first, $second], $listeners);
    }

    /**
     * The same listener may be registered more than once.
     *
     * @return void
     */
    public function testSameListenerMayBeAddedTwice(): void {
        $listener = fn(object $event): null => null;

        $this->provider->addListener(Event::class, $listener)
            ->addListener(Event::class, $listener);

        $this->assertCount(2, [...$this->provider->getListenersForEvent(new Event())]);
    }

    /**
     * Listeners may be registered using an event instance.
     *
     * @return void
     */
    public function testListenersRegisteredByInstance(): void {
        $this->provider->addListener(new Event(), fn(object $event): null => null);

        $this->assertCount(1, [...$this->provider->getListenersForEvent(new Event())]);
    }

    /**
     * Listeners are kept separate per event.
     *
     * @return void
     */
    public function testListenersAreScopedToEvent(): void {
        $this->provider->addListener(Event::class, fn(object $event): null => null);

        $this->assertSame([], [...$this->provider->getListenersForEvent(new EventProviderStub())]);
    }

    /**
     * `clearListeners()` removes all listeners for an event.
     *
     * @return void
     */
    public function testClearListeners(): void {
        $this->provider->addListener(Event::class, fn(object $event): null => null, 3)
            ->addListener(Event::class, fn(object $event): null => null, 7);

        $this->provider->clearListeners(Event::class);

        $this->assertSame([], [...$this->provider->getListenersForEvent(new Event())]);
    }
}

final class PrioritisedAttributedListenerStub {
    /** @var list<string> */
    public array $calls = [];

    #[Listener(Event::class, priority: 10)]
    public function high(Event $event): void {
        $this->calls[] = 'high';
    }

    #[Listener(Event::class)]
    public function first(Event $event): void {
        $this->calls[] = 'first';
    }

    #[Listener(Event::class)]
    public function second(Event $event): void {
        $this->calls[] = 'second';
    }

    #[Listener(Event::class, priority: -10)]
    public function low(Event $event): void {
        $this->calls[] = 'low';
    }
}
