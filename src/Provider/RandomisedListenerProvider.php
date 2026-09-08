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

namespace Inane\Event\Provider;

use InvalidArgumentException;

use function shuffle;

/**
 * RandomisedListenerProvider
 *
 * A listener provider that returns listeners in a randomised order.
 * Useful for testing or when the listener execution order should not be relied upon.
 *
 * @version 1.0.0
 */
class RandomisedListenerProvider extends ListenerProvider {
    /**
     * Get Listeners For Event
     *
     * Returns listeners in a randomised order.
     *
     * @param string|object $event Event class name or instance.
     *
     * @return iterable Listeners for the event, in a randomised order.
     *
     * @throws InvalidArgumentException
     */
    public function getListenersForEvent(string|object $event): iterable {
        // Unpacked into a list first as shuffle() needs an array by reference.
        $listeners = [...parent::getListenersForEvent($event)];
        shuffle($listeners);

        return $listeners;
    }
}
