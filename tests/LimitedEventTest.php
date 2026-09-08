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

use Inane\Event\LimitedEvent;
use Inane\Event\StoppableEvent;
use PHPUnit\Framework\TestCase;

/**
 * Tests for `Inane\Event\LimitedEvent`.
 *
 * Verifies that the event stops propagating once the configured limit of
 * propagation checks has been reached and that an explicit stop still
 * takes effect.
 */
final class LimitedEventTest extends TestCase {
    /**
     * A limited event is a stoppable event.
     *
     * @return void
     */
    public function testExtendsStoppableEvent(): void {
        $this->assertInstanceOf(StoppableEvent::class, new LimitedEvent());
    }

    /**
     * The default limit is a single propagation check.
     *
     * @return void
     */
    public function testDefaultLimit(): void {
        $event = new LimitedEvent();

        $this->assertSame(1, $event->limit);
        $this->assertSame(0, $event->count);
    }

    /**
     * A zero limit stops propagation before any listener is invoked.
     *
     * @return void
     */
    public function testZeroLimitStopsOnFirstCheck(): void {
        $event = new LimitedEvent(0);

        $this->assertTrue($event->isPropagationStopped());
        $this->assertSame(1, $event->count);
    }

    /**
     * Each propagation check increments the counter.
     *
     * @return void
     */
    public function testCountIncrementsWithEachCheck(): void {
        $event = new LimitedEvent(3);

        $event->isPropagationStopped();
        $event->isPropagationStopped();

        $this->assertSame(2, $event->count);
    }

    /**
     * Propagation stops once the limit has been reached.
     *
     * @return void
     */
    public function testPropagationStopsAtLimit(): void {
        $event = new LimitedEvent(2);

        $this->assertFalse($event->isPropagationStopped());
        $this->assertFalse($event->isPropagationStopped());
        $this->assertTrue($event->isPropagationStopped());
    }

    /**
     * An explicit stop halts propagation before the limit is reached.
     *
     * @return void
     */
    public function testExplicitStopOverridesLimit(): void {
        $event = new LimitedEvent(5);
        $event->stopPropagation();

        $this->assertTrue($event->isPropagationStopped());
    }
}
