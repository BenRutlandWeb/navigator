<?php

namespace Navigator\Notifications\Concerns;

enum NotificationStatus: string
{
    case READ = 'read';
    case UNREAD = 'unread';
}
