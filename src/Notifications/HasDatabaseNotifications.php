<?php

namespace Navigator\Notifications;

use Navigator\Database\Query\PostBuilder;
use Navigator\Notifications\Concerns\NotificationStatus;

trait HasDatabaseNotifications
{
    /** @return PostBuilder<DatabaseNotification> */
    public function notifications(): PostBuilder
    {
        return $this->hasMany(DatabaseNotification::class)->latest();
    }

    /** @return PostBuilder<DatabaseNotification> */
    public function readNotifications(): PostBuilder
    {
        return $this->notifications()->status(NotificationStatus::READ->value);
    }

    /** @return PostBuilder<DatabaseNotification> */
    public function unreadNotifications(): PostBuilder
    {
        return $this->notifications()->status(NotificationStatus::UNREAD->value);
    }
}
