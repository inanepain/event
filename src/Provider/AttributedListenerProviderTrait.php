<?php

declare(strict_types=1);

namespace Inane\Event\Provider;

use Inane\Event\Attribute\Listener as ListenerAttribute;
use InvalidArgumentException;
use ReflectionClass;

use function class_exists;
use function is_callable;

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