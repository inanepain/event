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

use Inane\Stdlib\Options;

use function is_object;

/**
 * Class ListenerProvider
 *
 * @version 1.0.0
 */
trait ListenerProviderTrait {
    #region Provider
    protected Options $eventListeners {
        get => $this->eventListeners ??= new Options();
        set => $this->eventListeners = $value;
    }

    protected static function getEventName(string|object $event): string {
        return is_object($event) ? $event::class : $event;
    }

    public function addListener(string|object $event, callable $listener): static {
        $event = static::getEventName($event);
        if (!$this->eventListeners->has($event)) $this->eventListeners->set($event, []);

        if (!$this->eventListeners->get($event)
            ->contains($listener, true)) $this->eventListeners->get($event)[] = $listener;

        return $this;
    }

    public function getListenersForEvent(string|object $event): iterable {
        $event = static::getEventName($event);
        if ($this->eventListeners->has($event)) return $this->eventListeners->get($event);

        return [];
    }

    public function clearListeners(string|object $event): void {
        $event = static::getEventName($event);
        if ($this->eventListeners->has($event)) $this->eventListeners->unset($event);
    }
    #endregion Provider
}
