<?php

namespace MarvinLabs\DiscordLogger;

use Illuminate\Config\Repository;
use MarvinLabs\DiscordLogger\Contracts\DiscordWebHook;
use MarvinLabs\DiscordLogger\Discord\Message;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger as Monolog;

class LogHandler extends AbstractProcessingHandler
{
    /** @var \MarvinLabs\DiscordLogger\Contracts\DiscordWebHook */
    private $discord;

    /** @var \Illuminate\Config\Repository */
    private $config;

    /** @var \MarvinLabs\DiscordLogger\Discord\Message|null */
    private $currentMessage;

    public function __construct(Repository $config, DiscordWebHook $discord, string $level)
    {
        parent::__construct(Monolog::toMonologLevel($level));

        $this->level = $level;
        $this->discord = $discord;
        $this->config = $config;
    }

    public function write(array $record)
    {
//        dd($record);
        $this->newMessage()
            ->messageContent($record)
            ->messageFrom($record)
            ->send();
    }

    protected function newMessage(): LogHandler
    {
        $this->currentMessage = Message::make();
        return $this;
    }

    protected function send(): void
    {
        $this->discord->send($this->currentMessage);
        $this->currentMessage = null;
    }

    protected function messageContent(array $record): LogHandler
    {
        $appName = $this->config->get('app.name', 'laravel');
        $timestamp = $record['datetime']->format('Y-m-d H:i:s');
        $this->currentMessage->content("[$timestamp] $appName.{$record['level_name']}");

        return $this;
    }

    protected function messageFrom(array $record): LogHandler
    {
        $name = $this->getFromName();
        if ($name === null)
        {
            return $this;
        }

        $this->currentMessage->from($name, $this->getFromAvatar());

        return $this;
    }

    protected function getFromName(): string
    {
        return $this->config->get('discord-logger.from.name');
    }

    protected function getFromAvatar(): ?string
    {
        return $this->config->get('discord-logger.from.avatar_url');
    }
}
