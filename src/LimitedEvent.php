<?php

/**
 * LimitedEvent
 *
 * Inane Library
 *
 * $Id$
 * $Date$
 *
 * PHP version 8.5
 *
 * @author   Philip Michael Raab <philip@cathedral.co.za>
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

/**
 * LimitedEvent
 *
 * A stoppable event that halts propagation once it has been handed to a set
 * number of listeners.
 *
 * @version 0.1.0
 */
class LimitedEvent extends StoppableEvent {
    /**
     * Listener Count
     *
     * @var int Number of listeners the event has been offered to
     */
    protected(set) int $count = 0;

    /**
     * Limit Reached
     *
     * Reading this property consumes one of the allowed dispatches.
     *
     * @var bool Whether the allowed number of listeners has been exhausted
     */
    protected bool $limited {
        get => $this->count++ >= $this->limit;
    }

    /**
     * LimitedEvent constructor
     *
     * @param int $limit Maximum number of listeners allowed to handle the event
     */
    public function __construct(
        /**
         * Listener Limit
         *
         * @var int
         */
        public readonly int $limit = 1
    ) {}

    /**
     * Is Propagation Stopped
     *
     * @return bool Whether no further event listeners should be triggered
     */
    public function isPropagationStopped(): bool {
        // `$limited` is evaluated first so that each check counts against the limit.
        return $this->limited || $this->propagationStopped;
    }
}
