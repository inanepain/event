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
 * @author Philip Michael Raab<philip@cathedral.co.za>
 * @package inanepain\event
 * @category event
 *
 * @license UNLICENSE
 * @license https://unlicense.org/UNLICENSE UNLICENSE
 *
 * _version_ $version
 */

declare(strict_types=1);

namespace Inane\Event\Attribute;

use Attribute;

/**
 * Attribute class used to mark a method as a listener for a specific event.
 * This attribute is intended to declare methods that should respond to an event,
 * specifying the event type and the execution priority of the listener.
 * Methods marked with this attribute can handle events of the type specified in the
 * `event` parameter. The `priority` parameter determines the order of execution
 * relative to other listeners for the same event.
 */
#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
readonly class Listener {
    /**
     * Constructor for initializing the event and priority.
     *
     * @param string $event    The name of the event.
     * @param int    $priority The priority of the event, default is 0.
     *
     * @return void
     */
    public function __construct(
        public string $event,
        public int $priority = 0,
    ) {}
}