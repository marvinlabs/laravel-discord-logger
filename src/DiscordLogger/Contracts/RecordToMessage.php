<?php

namespace MarvinLabs\DiscordLogger\Contracts;

interface RecordToMessage
{
    public const ALLOWED_STACKTRACE_MODES = ['smart', 'inline', 'file'];

    public function buildMessages(array $record): array;
}
