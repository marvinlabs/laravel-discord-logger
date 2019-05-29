<?php

namespace MarvinLabs\DiscordLogger\Contracts;

use MarvinLabs\DiscordLogger\Discord\Message;

interface DiscordWebHook
{
    public function send(Message $message): void;

    public function getUrl(): string;
}
