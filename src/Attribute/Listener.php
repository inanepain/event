<?php

declare(strict_types=1);

namespace Inane\Event\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
readonly class Listener {
    /**
     * @param class-string<object> $event Event class handled by the method.
     * @param int                  $priority Listener priority.
     */
    public function __construct(
        public string $event,
        public int $priority = 0,
    ) {}
}