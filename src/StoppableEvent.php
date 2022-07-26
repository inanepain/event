<?php

/**
 * Inane
 *
 * Event
 *
 * PHP version 8.1
 *
 * @package Inane\Event
 * @author Philip Michael Raab<peep@inane.co.za>
 *
 * @license UNLICENSE
 * @license https://github.com/inanepain/event/raw/develop/UNLICENSE UNLICENSE
 *
 * @version $Id$
 * $Date$
 */

declare(strict_types=1);

namespace Inane\Event;

use Psr\EventDispatcher\StoppableEventInterface;

/**
 * Class Event
 *
 * @package Inane\Event
 *
 * @version 1.0.0
 */
class StoppableEvent extends Event implements StoppableEventInterface {

    /**
     * Propagation Stopped
     *
     * @var bool Whether no further event listeners should be triggered
     */
    private bool $propagationStopped = false;

    /**
     * Is propagation stopped?
     *
     * This will typically only be used by the Dispatcher to determine if the
     * previous listener halted propagation.
     *
     * @return bool
     *   True if the Event is complete and no further listeners should be called.
     *   False to continue calling listeners.
     */
    public function isPropagationStopped(): bool {
        return $this->propagationStopped;
    }

    /**
     * Stops the propagation of the event to further event listeners.
     *
     * If multiple event listeners are connected to the same event, no
     * further event listener will be triggered once any trigger calls
     * stopPropagation().
     */
    public function stopPropagation(): void {
        $this->propagationStopped = true;
    }
}
