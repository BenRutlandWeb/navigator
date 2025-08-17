<?php

namespace Navigator\Schedule;

use Navigator\Events\Dispatcher;
use Navigator\Foundation\Application;
use Navigator\Foundation\ServiceProvider;
use Navigator\Schedule\Console\Commands\ScheduleList;
use Navigator\Schedule\Console\Commands\ScheduleRun;
use Navigator\Schedule\Console\Commands\ScheduleTest;

class ScheduleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Schedule::class, function (Application $app) {
            return new Schedule($app->get(Dispatcher::class));
        });
    }

    public function boot(): void
    {
        $this->schedule($schedule = $this->app->get(Schedule::class));

        $schedule->registerScheduledEvents();

        $this->commands([
            ScheduleList::class,
            ScheduleRun::class,
            ScheduleTest::class,
        ]);
    }

    public function schedule(Schedule $schedule): void
    {
        //
    }
}
