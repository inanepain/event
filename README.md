# Inane\Event

Both examples are basically the same but the second extends the provider for the application.

## Example 1:

```php
class Odd extends \Inane\Event\StoppableEvent {
    public function __construct(
        protected string $message = ''
    ) {
    }

    public function getMessage(): string {
        return $this->message;
    }
}

$provider = new \Inane\Event\ListenerProvider();
$dispatcher = new \Inane\Event\EventDispatcher($provider);

$provider->addListener(\Inane\Event\Event::class, fn ($event) => print_r($event));
$provider->addListener(\Inane\Event\Event::class, function($event) use ($dispatcher) {
    echo "START:\n";

    foreach([0,1,2,3,4,5,6,7,8,9] as $i) {
        echo "Current: {$i}\n";
        if($i & 1) $dispatcher->dispatch(new Odd("{$i}"));
        echo "\n";
    }
});

$provider->addListener(Odd::class, function ($event) {
    if ($event->getMessage() > 5) $event->stopPropagation();
    echo "Odd: " . $event->getMessage() . "\n";
});

$provider->addListener(Odd::class, function ($event) {
    echo "Not Even: " . $event->getMessage() . "\n";
});

$startEvent = new \Inane\Event\Event();
$dispatcher->dispatch($startEvent);
```

## Example 2:

```php
class Odd extends \Inane\Event\StoppableEvent {
    public function __construct(
        protected string $message = ''
    ) {
    }

    public function getMessage(): string {
        return $this->message;
    }
}

class Numbers extends \Inane\Event\ListenerProvider {
    private \Inane\Event\EventDispatcher $dispatcher;

    public function __construct() {
        $this->dispatcher = new \Inane\Event\EventDispatcher($this);
    }

    public function start() {
        foreach ([0, 1, 2, 3, 4, 5, 6, 7, 8, 9] as $i) {
            echo "Current: {$i}\n";
            if ($i & 1) $this->dispatcher->dispatch(new Odd("{$i}"));
            echo "\n";
        }
    }
}

$num = new Numbers();

$num->addListener(Odd::class, function ($event) {
    if ($event->getMessage() > 5) $event->stopPropagation();
    echo "Odd: " . $event->getMessage() . "\n";
});

$num->addListener(Odd::class, function ($event) {
    echo "Not Even: " . $event->getMessage() . "\n";
});

$num->start();
```
