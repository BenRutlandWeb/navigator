<?php

namespace Navigator\Notifications;

use Navigator\Database\Models\Concerns\HasContent;
use Navigator\Database\Models\Concerns\HasTitle;
use Navigator\Database\Models\Concerns\IsPrivate;
use Navigator\Database\Models\Post as Model;
use Navigator\Database\Query\PostBuilder;

class DatabaseNotification extends Model
{
    use HasContent;
    use HasTitle;
    use IsPrivate;

    public static function withGlobalScopes(PostBuilder $query): void
    {
        $query->status(['read', 'unread']);
    }

    public function markAsRead(): void
    {
        $this->setPostStatus('read');
    }

    public function markAsUnead(): void
    {
        $this->setPostStatus('unread');
    }

    public function read(): bool
    {
        return $this->hasPostStatus('read');
    }

    public function unread(): bool
    {
        return $this->hasPostStatus('unread');
    }
}
