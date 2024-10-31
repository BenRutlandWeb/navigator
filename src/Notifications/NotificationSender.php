<?php

namespace Navigator\Notifications;

use Navigator\Contracts\ShouldQueue;

class NotificationSender
{
    public function __construct(protected ChannelManager $manager)
    {
        //
    }

    public function send(mixed $notifiables, NotificationInterface $notification): void
    {
        if ($notification instanceof ShouldQueue) {
            $this->queueNotification($notifiables, $notification);
            return;
        }

        $this->sendNow($notifiables, $notification);
    }

    public function sendNow(mixed $notifiables, NotificationInterface $notification, ?array $channels = null): void
    {
        foreach ($this->formatNotifiables($notifiables) as $notifiable) {
            $channels = $channels ?? $notification->via($notifiable);

            foreach ($channels as $channel) {
                $this->manager->get($channel)->send($notifiable, $notification);
            }
        }
    }

    public function queueNotification(mixed $notifiables, NotificationInterface $notification): void
    {
        foreach ($this->formatNotifiables($notifiables) as $notifiable) {
            $channels = $notification->via($notifiable);

            foreach ($channels as $channel) {
                $dispatch = SendQueuedNotifications::dispatch($this, $notifiables, $notification, [$channel]);

                if (isset($notification->delay)) {
                    $dispatch->delay($notification->delay);
                }
            }
        }
    }

    public function formatNotifiables(mixed $notifiables): iterable
    {
        return is_iterable($notifiables) ? $notifiables : [$notifiables];
    }
}
