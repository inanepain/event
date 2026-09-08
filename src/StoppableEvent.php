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

namespace Inane\Event;

use Psr\EventDispatcher\StoppableEventInterface;

/**
 * Class Event
 *
 * @version 1.0.0
 */
class StoppableEvent extends Event implements StoppableEventInterface {
    /**
     * Propagation Stopped
     *
     * @var bool Whether no further event listeners should be triggered
     */
    protected bool $propagationStopped = false {
        get => $this->propagationStopped;
        set => $this->propagationStopped = $value;
    }

    /**
     * Is Propagation Stopped?
     *
     * @return bool Whether no further event listeners should be triggered
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
