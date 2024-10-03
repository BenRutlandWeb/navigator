<?php

namespace Navigator\Notifications;

use Navigator\Database\Models\Concerns\HasSerializedContent;
use Navigator\Database\Models\Concerns\HasTitle;
use Navigator\Database\Models\Concerns\IsPrivate;
use Navigator\Database\Models\Post as Model;
use Navigator\Database\Query\PostBuilder;
use Navigator\Notifications\Concerns\NotificationStatus;

class DatabaseNotification extends Model
{
    use HasSerializedContent;
    use HasTitle;
    use IsPrivate;

    public static function withGlobalScopes(PostBuilder $query): void
    {
        $query->status([NotificationStatus::READ->value, NotificationStatus::UNREAD->value]);
    }

    public function markAsRead(): void
    {
        $this->setPostStatus(NotificationStatus::READ->value);
    }

    public function markAsUnread(): void
    {
        $this->setPostStatus(NotificationStatus::UNREAD->value);
    }

    public function read(): bool
    {
        return $this->hasPostStatus(NotificationStatus::READ->value);
    }

    public function unread(): bool
    {
        return $this->hasPostStatus(NotificationStatus::UNREAD->value);
    }
}
