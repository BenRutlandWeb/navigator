<?php

namespace Navigator\Notifications;

use Navigator\Notifications\Channels\MailChannel;

class ChannelManager
{
    /** @var array<string, ChannelInterface> */
    protected array $channels = [];

    public function addChannel(string $alias, ChannelInterface $channel): void
    {
        $this->channels[$alias] = $channel;
    }

    public function get(string $alias): ChannelInterface
    {
        return $this->channels[$alias] ?? $this->channels[MailChannel::class];
    }
}
