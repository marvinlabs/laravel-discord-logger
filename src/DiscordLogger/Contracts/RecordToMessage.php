<?php

namespace MarvinLabs\DiscordLogger\Contracts;

use MarvinLabs\DiscordLogger\Discord\Message;

interface RecordToMessage
{
    public const ALLOWED_STACKTRACE_MODES = ['smart', 'inline', 'file'];

    public function buildMessage(array $record): Message;
}
