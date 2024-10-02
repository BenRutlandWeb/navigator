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

    /** @return PostBuilder<DatabaseNotification> */
    public function readNotifications(): PostBuilder
    {
        return $this->notifications()->status('read');
    }

    /** @return PostBuilder<DatabaseNotification> */
    public function unreadNotifications(): PostBuilder
    {
        return $this->notifications()->status('unread');
    }
}
