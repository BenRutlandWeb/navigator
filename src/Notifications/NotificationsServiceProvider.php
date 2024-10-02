<?php

namespace Navigator\Notifications;

use Navigator\Database\Relation;
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

        $this->app->get(WordPressFactory::class)->registerPostType(DatabaseNotification::class);
    }
}
