<?php

namespace Navigator\Notifications;

use Navigator\Database\Relation;
use Navigator\Events\Dispatcher;
use Navigator\Foundation\ServiceProvider;
use Navigator\WordPress\WordPressFactory;

class NotificationsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Relation::addMorphedModel('notification', DatabaseNotification::class);

        $this->app->get(Dispatcher::class)->listen('init', function () {
            register_post_status('read', ['internal' => true]);
            register_post_status('unread', ['internal' => true]);
            $this->app->get(WordPressFactory::class)->registerPostType(DatabaseNotification::class);
        });
    }
}
