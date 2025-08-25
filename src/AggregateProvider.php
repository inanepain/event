<?php

/**
 * Inane: Event
 *
 * PSR-14 implementation: event dispatcher.
 *
 * $Id$
 * $Date$
 *
 * PHP version 8.4
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
