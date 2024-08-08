<?php

namespace Navigator\Events;

use Navigator\Contracts\SubscriberInterface;
use Navigator\Events\Console\Commands\MakeListener;
use Navigator\Events\Console\Commands\MakeSubscriber;
use Navigator\Foundation\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * @var array<string, array<class-string|callable>>
     */
    protected array $actions = [];

    /**
     * @var class-string<SubscriberInterface>[]
     */
    protected array $subscribers = [];

    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $dispatcher = $this->app->get(Dispatcher::class);

        foreach ($this->actions as $action => $listeners) {
            foreach ($listeners as $listener) {
                if (property_exists($listener, 'priority')) {
                    $dispatcher->listen($action, $listener, $listener::$priority ?? 10);
                } else {
                    $dispatcher->listen($action, $listener);
                }
            }
        }

        foreach ($this->subscribers as $subscriber) {
            $dispatcher->subscribe($subscriber);
        }

        $this->commands([
            MakeListener::class,
            MakeSubscriber::class,
        ]);
    }
}
