<?php

namespace MarvinLabs\DiscordLogger\Converters;

use Illuminate\Contracts\Config\Repository;
use function in_array;
use MarvinLabs\DiscordLogger\Contracts\DiscordWebHook;
use MarvinLabs\DiscordLogger\Contracts\RecordToMessage;
use MarvinLabs\DiscordLogger\Discord\Exceptions\ConfigurationIssue;
use MarvinLabs\DiscordLogger\Discord\Message;
use Throwable;

abstract class AbstractRecordConverter implements RecordToMessage
{
    /** @var \Illuminate\Contracts\Config\Repository */
    protected $config;

    public function __construct(Repository $config)
    {
        $this->config = $config;
    }

    /**
     * @throws \MarvinLabs\DiscordLogger\Discord\Exceptions\ConfigurationIssue
     */
    protected function stackTraceMode(string $stacktrace): string
    {
        $value = (string)$this->config->get('discord-logger.stacktrace', 'smart');

        if (!in_array($value, RecordToMessage::ALLOWED_STACKTRACE_MODES, true))
        {
            throw new ConfigurationIssue("Invalid value for configuration `discord-logger.stacktrace`: $value");
        }

        if ($value === 'smart')
        {
            if (strlen($stacktrace) < DiscordWebHook::MAX_CONTENT_LENGTH)
            {
                $value = 'inline';
            }
            else
            {
                $value = 'file';
            }
        }

        return $value;
    }

    protected function getStacktraceFilename(array $record): ?string
    {
        $timestamp = $record['datetime']->format('YmdHis');
        return "{$timestamp}_stacktrace.txt";
    }

    protected function getStacktrace(array $record): ?string
    {
        if (empty($record['context'])
            || empty($record['context']['exception'])
            || !is_a($record['context']['exception'], Throwable::class))
        {
            return null;
        }

        /** @var \Throwable $exception */
        $exception = $record['context']['exception'];

        return $exception->getTraceAsString();
    }

    protected function getRecordColor(array $record): int
    {
        $colors = $this->config->get('discord-logger.colors', []);

        return $colors[$record['level_name']] ?? 0x666666;
    }

    protected function getRecordEmoji(array $record): ?string
    {
        $colors = $this->config->get('discord-logger.emojis', []);

        return $colors[$record['level_name']] ?? null;
    }

    protected function addGenericMessageFrom(Message $message): void
    {
        $name = $this->getFromName();
        if ($name === null)
        {
            return;
        }

        $message->from($name, $this->getFromAvatar());
    }

    protected function getFromName(): ?string
    {
        return $this->config->get('discord-logger.from.name');
    }

    protected function getFromAvatar(): ?string
    {
        return $this->config->get('discord-logger.from.avatar_url');
    }
}
