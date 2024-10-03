<?php

namespace Navigator\Notifications;

use Navigator\Queue\Job;

class SendQueuedNotifications extends Job
{
    public function __construct(
        protected NotificationSender $sender,
        protected mixed $notifiable,
        protected NotificationInterface $notification,
        protected array $channels
    ) {
        //
    }

    public function handle(): void
    {
        $this->sender->sendNow($this->notifiable, $this->notification, $this->channels);
    }
}
