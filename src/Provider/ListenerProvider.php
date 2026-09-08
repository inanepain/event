<?php

/**
 * Inane: Event
 * PSR-14 implementation: event dispatcher.
 * $Id$
 * $Date$
 * PHP version 8.5
 *
 * @author   Philip Michael Raab<philip@cathedral.co.za>
 * @package  inanepain\event
 * @category event
 * @license  UNLICENSE
 * @license  https://unlicense.org/UNLICENSE UNLICENSE
 * _version_ $version
 */

declare(strict_types = 1);

namespace Inane\Event\Provider;

use Psr\EventDispatcher\ListenerProviderInterface;

/**
 * ListenerProvider
 * The default listener provider: returns the listeners registered for an event
 * in the order they were added.
 *
 * @version 1.0.0
 */
class ListenerProvider implements ListenerProviderInterface {
    use AttributedListenerProviderTrait;
    use ListenerProviderTrait;

    /**
     * Add Attributed Listener Binding
     * Registers a binding without changing insertion order.
     *
     * @param class-string<object> $event    Event class handled by the method.
     * @param callable             $listener Listener callable.
     * @param int                  $priority Listener priority.
     *
     * @return void
     */
    protected function addAttributedListenerBinding(string $event, callable $listener, int $priority): void {
        $this->addListener($event, $listener);
    }
}
