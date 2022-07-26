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

use Psr\EventDispatcher\ListenerProviderInterface;

/**
 * AggregateProvider
 *
 * An aggregate provider encapsulates multiple other providers and concatenates their responses.
 *
 * Be aware that any ordering of listeners in different sub-providers is ignored, and providers are
 * iterated in the order in which they were added.  That is, all listeners from the first provider
 * added will be returned to the caller, then all listeners from the second provider, and so on.
 *
 * @package Inane\Event
 *
 * @version 1.0.0
 */
class AggregateProvider implements ListenerProviderInterface {
    /**
     * Providers
     *
     * @var array
     */
    protected array $providers = [];

    /**
     * Get Listeners For Event
     *
     * @param object $event
     *
     * @return iterable
     */
    public function getListenersForEvent(object $event): iterable {
        /** @var ListenerProviderInterface $provider */
        foreach ($this->providers as $provider) yield from $provider->getListenersForEvent($event);
    }

    /**
     * Enqueues a listener provider to this set.
     *
     * @param ListenerProviderInterface $provider The provider to add.
     *
     * @return AggregateProvider The called object.
     */
    public function addProvider(ListenerProviderInterface $provider): self {
        $this->providers[] = $provider;
        return $this;
    }
}
