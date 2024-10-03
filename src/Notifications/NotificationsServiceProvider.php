<?php

namespace Navigator\Notifications;

use Navigator\Database\Relation;
use Navigator\Events\Dispatcher;
use Navigator\Foundation\Application;
use Navigator\Foundation\ServiceProvider;
use Navigator\Notifications\Channels\DatabaseChannel;
use Navigator\Notifications\Channels\MailChannel;
use Navigator\Notifications\Concerns\NotificationStatus;
use Navigator\Notifications\Console\Commands\MakeNotification;
use Navigator\WordPress\WordPressFactory;

class NotificationsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ChannelManager::class, function () {
            $manager = new ChannelManager();

            $manager->addChannel(DatabaseChannel::class, new DatabaseChannel());
            $manager->addChannel(MailChannel::class, new MailChannel());

            return $manager;
        });

        $this->app->singleton(NotificationSender::class, function (Application $app) {
            $sender = new NotificationSender($app->get(ChannelManager::class));

            Notification::setNotificationSender($sender);

            return $sender;
        });
    }

    public function boot(): void
    {
        $this->commands([
            MakeNotification::class,
        ]);

        $this->registerDatabaseNotificationPostType();
    }

    public function registerDatabaseNotificationPostType(): void
    {
        Relation::addMorphedModel('notification', DatabaseNotification::class);

        $this->app->get(Dispatcher::class)->listen('init', function () {
            register_post_status(NotificationStatus::READ->value, ['internal' => true]);
            register_post_status(NotificationStatus::UNREAD->value, ['internal' => true]);
            $this->app->get(WordPressFactory::class)->registerPostType(DatabaseNotification::class);
        });
    }
}
