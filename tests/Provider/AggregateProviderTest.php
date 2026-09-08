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
    AggregateProvider,
    ListenerProvider};
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\ListenerProviderInterface;

/**
 * Tests for `Inane\Event\Provider\AggregateProvider`.
 *
 * Verifies that listeners from all sub providers are concatenated in the
 * order the providers were added.
 */
final class AggregateProviderTest extends TestCase {
    /**
     * Aggregate Provider
     *
     * @var AggregateProvider
     */
    private AggregateProvider $provider;

    /**
     * Creates a fresh provider for each test.
     *
     * @return void
     */
    protected function setUp(): void {
        $this->provider = new AggregateProvider();
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
     * Without sub providers no listeners are returned.
     *
     * @return void
     */
    public function testEmptyAggregateReturnsNoListeners(): void {
        $this->assertSame([], [...$this->provider->getListenersForEvent(new Event())]);
    }

    /**
     * `addProvider()` is fluent.
     *
     * @return void
     */
    public function testAddProviderIsFluent(): void {
        $result = $this->provider->addProvider(new ListenerProvider());

        $this->assertSame($this->provider, $result);
    }

    /**
     * Listeners are concatenated in provider order.
     *
     * @return void
     */
    public function testListenersConcatenatedInProviderOrder(): void {
        $first = fn(object $event): string => 'first';
        $second = fn(object $event): string => 'second';
        $third = fn(object $event): string => 'third';

        $one = (new ListenerProvider())->addListener(Event::class, $first);
        $two = (new ListenerProvider())->addListener(Event::class, $second)
            ->addListener(Event::class, $third);

        $this->provider->addProvider($one)
            ->addProvider($two);

        $listeners = [...$this->provider->getListenersForEvent(new Event())];

        $this->assertSame([$first, $second, $third], $listeners);
    }

    /**
     * Only listeners matching the event are aggregated.
     *
     * @return void
     */
    public function testAggregateRespectsEventScope(): void {
        $listener = fn(object $event): null => null;

        $this->provider->addProvider((new ListenerProvider())->addListener(Event::class, $listener));

        $this->assertSame([], [...$this->provider->getListenersForEvent(new EventProviderStub())]);
    }
}
