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

use Inane\Event\StoppableEvent;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\StoppableEventInterface;

/**
 * Tests for `Inane\Event\StoppableEvent`.
 *
 * Verifies the propagation flag defaults and that propagation can be
 * stopped as described by PSR-14.
 */
final class StoppableEventTest extends TestCase {
    /**
     * A stoppable event satisfies the PSR-14 contract.
     *
     * @return void
     */
    public function testImplementsStoppableEventInterface(): void {
        $this->assertInstanceOf(StoppableEventInterface::class, new StoppableEvent());
    }

    /**
     * Propagation is not stopped on a freshly created event.
     *
     * @return void
     */
    public function testPropagationNotStoppedByDefault(): void {
        $event = new StoppableEvent();

        $this->assertFalse($event->isPropagationStopped());
    }

    /**
     * Calling `stopPropagation()` flags the event as stopped.
     *
     * @return void
     */
    public function testStopPropagation(): void {
        $event = new StoppableEvent();
        $event->stopPropagation();

        $this->assertTrue($event->isPropagationStopped());
    }

    /**
     * Stopping propagation more than once is harmless.
     *
     * @return void
     */
    public function testStopPropagationIsIdempotent(): void {
        $event = new StoppableEvent();
        $event->stopPropagation();
        $event->stopPropagation();

        $this->assertTrue($event->isPropagationStopped());
    }
}
