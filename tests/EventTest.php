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

use Inane\Event\Event;
use PHPUnit\Framework\TestCase;

/**
 * A custom event used to verify name resolution for subclasses.
 */
class CustomEvent extends Event {}

/**
 * Tests for `Inane\Event\Event`.
 *
 * Verifies the default event name resolution provided by the `name`
 * property hook.
 */
final class EventTest extends TestCase {
    /**
     * The event name defaults to the class name.
     *
     * @return void
     */
    public function testNameDefaultsToClassName(): void {
        $event = new Event();

        $this->assertSame(Event::class, $event->name);
    }

    /**
     * Subclasses report their own class name as the event name.
     *
     * @return void
     */
    public function testNameUsesLateStaticBinding(): void {
        $event = new CustomEvent();

        $this->assertSame(CustomEvent::class, $event->name);
    }

    /**
     * Repeated reads of the name return a stable value.
     *
     * @return void
     */
    public function testNameIsStableBetweenReads(): void {
        $event = new CustomEvent();

        $this->assertSame($event->name, $event->name);
    }
}
