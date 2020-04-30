<?php

namespace MarvinLabs\DiscordLogger\Converters;

use Illuminate\Support\Arr;
use MarvinLabs\DiscordLogger\Discord\Embed;
use MarvinLabs\DiscordLogger\Discord\Exceptions\ConfigurationIssue;
use MarvinLabs\DiscordLogger\Discord\Message;

class RichRecordConverter extends AbstractRecordConverter
{
    /**
     * @throws \MarvinLabs\DiscordLogger\Discord\Exceptions\ConfigurationIssue
     */
    public function buildMessages(array $record): array
    {
        $mainMessage = Message::make();

        $this->addGenericMessageFrom($mainMessage);
        $this->addMainEmbed($mainMessage, $record);
        $this->addContextEmbed($mainMessage, $record);
        $this->addExtrasEmbed($mainMessage, $record);

        $stackTraceMessage = null;
        $stacktrace = $this->getStacktrace($record);
        if ($stacktrace !== null)
        {
            switch ($this->stackTraceMode($stacktrace))
            {
                case 'file':
                    // Discord webhooks do not support EMBED + FILE at the same time. Hence another message has to be sent
                    $stackTraceMessage = Message::make()->file($stacktrace, $this->getStacktraceFilename($record));
                    $this->addGenericMessageFrom($stackTraceMessage);
                    break;

                case 'inline' :
                    $this->addInlineMessageStacktrace($mainMessage, $record, $stacktrace);
                    break;

                default:
                    throw new ConfigurationIssue('Invalid value for configuration `discord-logger.stacktrace`');
            }
        }

        return $stackTraceMessage !== null ? [$mainMessage, $stackTraceMessage] : [$mainMessage];
    }

    protected function addMainEmbed(Message $message, array $record): void
    {
        $timestamp = $record['datetime']->format('Y-m-d H:i:s');
        $title = "[$timestamp] {$record['channel']}.{$record['level_name']}";
        $description = $record['message'];
        $emoji = $this->getRecordEmoji($record);

        $message->embed(Embed::make()
            ->color($this->getRecordColor($record))
            ->title($emoji === null ? "`$title`" : "$emoji `$title`")
            ->description($emoji === null ? "`$description`" : ":black_small_square: `$description`"));
    }

    protected function addContextEmbed(Message $message, array $record): void
    {
        $context = Arr::except($record['context'] ?? [], ['exception']);
        if (empty($context))
        {
            return;
        }

        $message->embed(Embed::make()
            ->color($this->getRecordColor($record))
            ->description("**Context**\n`" . json_encode($context, JSON_PRETTY_PRINT) . '`'));
    }

    protected function addExtrasEmbed(Message $message, array $record): void
    {
        $extras = $record['extra'] ?? [];
        if (empty($extras))
        {
            return;
        }

        $message->embed(Embed::make()
            ->color($this->getRecordColor($record))
            ->description("**Extra**\n`" . json_encode($extras, JSON_PRETTY_PRINT) . '`'));
    }

    protected function addInlineMessageStacktrace(Message $message, array $record, string $stacktrace): void
    {
        $message->embed(Embed::make()
            ->color($this->getRecordColor($record))
            ->title('Stacktrace')
            ->description("`$stacktrace`"));
    }
}
