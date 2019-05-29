<?php

namespace MarvinLabs\DiscordLogger\Contracts;

use MarvinLabs\DiscordLogger\Discord\Message;

interface DiscordClient
{
    public function send(Message $message): void;
}
