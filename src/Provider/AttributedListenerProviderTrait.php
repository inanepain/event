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

use Inane\Event\Attribute\Listener as ListenerAttribute;
use InvalidArgumentException;
use ReflectionClass;

use function class_exists;
use function is_callable;
use function sprintf;

/**
 * Trait AttributedListenerProviderTrait
 * Provides functionality to register attributed listeners, enabling discovery
 * and binding of methods within listener objects annotated with specific attributes.
 */
trait AttributedListenerProviderTrait {
    /**
     * Add Attributed Listener Binding
     * Registers a discovered listener binding.
     *
     * @param class-string<object> $event    Event class handled by the method.
     * @param callable             $listener Listener callable.
     * @param int                  $priority Listener priority.
     *
     * @return void
     */
    abstract protected function addAttributedListenerBinding(string $event, callable $listener, int $priority): void;

    /**
     * Add Attributed Listener
     * Registers public methods annotated with Listener attributes.
     *
     * @param object $listener Listener object containing attributed methods.
     *
     * @return static The called object.
     *
     * @throws InvalidArgumentException When an attributed declaration is invalid.
     */
    public function addAttributedListener(object $listener): static {
        $reflection = new ReflectionClass($listener);

        foreach ($reflection->getMethods() as $method) {
            $attributes = $method->getAttributes(ListenerAttribute::class);
            if ($attributes === []) continue;

            if (!$method->isPublic()) {
                throw new InvalidArgumentException(sprintf(
                    'Attributed listener method %s::%s() must be public.',
                    $reflection->getName(),
                    $method->getName(),
                ));
            }

            $callable = [$listener, $method->getName()];
            if (!is_callable($callable)) {
                throw new InvalidArgumentException(sprintf(
                    'Attributed listener method %s::%s() is not callable.',
                    $reflection->getName(),
                    $method->getName(),
                ));
            }

            foreach ($attributes as $attribute) {
                // Each repeatable attribute creates an independent event binding.
                $declaration = $attribute->newInstance();
                if (!class_exists($declaration->event)) {
                    throw new InvalidArgumentException(sprintf(
                        'Attributed listener event %s must name an existing class.',
                        $declaration->event,
                    ));
                }

                $this->addAttributedListenerBinding($declaration->event, $callable, $declaration->priority);
            }
        }

        return $this;
    }
}