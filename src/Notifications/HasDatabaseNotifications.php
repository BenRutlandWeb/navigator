<?php

namespace Navigator\Notifications;

use Navigator\Database\Query\PostBuilder;

trait HasDatabaseNotifications
{
    /** @return PostBuilder<DatabaseNotification> */
    public function notifications(): PostBuilder
    {
        return $this->hasMany(DatabaseNotification::class);
    }

    public function readNotifications(): PostBuilder
    {
        return $this->notifications()->status('read');
    }

    public function unreadNotifications(): PostBuilder
    {
        return $this->notifications()->status('unread');
    }
}
