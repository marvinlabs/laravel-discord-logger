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

    public function __construct(Repository $config, DiscordWebHook $discord, int $level)
    {
        parent::__construct(Monolog::toMonologLevel($level));

        $this->level = $level;
        $this->discord = $discord;
        $this->config = $config;
    }

    /**
     * @param array $record
     */
    public function write(array $record)
    {
        $this->appName = $this->config->get('app.name', 'My Application');
        $this->appEnv = $this->config->get('app.env', 'local');

        $this->discord->send(Message::make()
            ->content($this->config->get('app.name', 'My Application') . ' / ' . $this->config->get('app.env',
                    'local')));
    }
}
