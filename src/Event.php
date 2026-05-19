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

namespace Inane\Event;

/**
 * Event
 *
 * Base event class. Extend this to create custom events.
 *
 * @version 1.0.0
 */
class Event {
    /**
     * Event name
     *
     * Defaults to the class name if not provided.
     *
     * @var string
     */
    public string $name {
        get => $this->name ?? $this->name = static::class;
    }
}
