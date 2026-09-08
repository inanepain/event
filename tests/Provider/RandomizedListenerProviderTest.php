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

use Inane\Event\Event;
use Inane\Event\Provider\{
    ListenerProvider,
    RandomizedListenerProvider};
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\ListenerProviderInterface;

/**
 * Tests for `Inane\Event\Provider\RandomizedListenerProvider`.
 *
 * Verifies that the complete listener set is returned, regardless of the
 * randomised order.
 */
final class RandomizedListenerProviderTest extends TestCase {
    /**
     * Randomized Listener Provider
     *
     * @var RandomizedListenerProvider
     */
    private RandomizedListenerProvider $provider;

    /**
     * Creates a fresh provider for each test.
     *
     * @return void
     */
    protected function setUp(): void {
        $this->provider = new RandomizedListenerProvider();
    }

    /**
     * The provider extends the standard listener provider.
     *
     * @return void
     */
    public function testExtendsListenerProvider(): void {
        $this->assertInstanceOf(ListenerProvider::class, $this->provider);
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
     * Every registered listener is returned exactly once.
     *
     * @return void
     */
    public function testAllListenersReturnedRegardlessOfOrder(): void {
        $first = fn(object $event): string => 'first';
        $second = fn(object $event): string => 'second';
        $third = fn(object $event): string => 'third';

        $this->provider->addListener(Event::class, $first)
            ->addListener(Event::class, $second)
            ->addListener(Event::class, $third);

        $listeners = [...$this->provider->getListenersForEvent(new Event())];

        $this->assertCount(3, $listeners);
        $this->assertContains($first, $listeners);
        $this->assertContains($second, $listeners);
        $this->assertContains($third, $listeners);
    }

    /**
     * Listeners may be requested using the event class name.
     *
     * @return void
     */
    public function testListenersMayBeRequestedByClassName(): void {
        $this->provider->addListener(Event::class, fn(object $event): null => null);

        $this->assertCount(1, [...$this->provider->getListenersForEvent(Event::class)]);
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
}
