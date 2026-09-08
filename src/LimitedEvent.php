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
 * inane-fw
 *
 * @version 0.1.0
 */
class LimitedEvent extends StoppableEvent {
    protected(set) int $count = 0;

    protected bool $limited {
        get => $this->count++ >= $this->limit;
    }

    public function __construct(
        public readonly int $limit = 1
    ) {}

    /**
     * Is Propagation Stopped
     *
     * @return bool Whether no further event listeners should be triggered
     */
    public function isPropagationStopped(): bool {
        return $this->limited || $this->propagationStopped;
    }
}
