<?php

namespace MarvinLabs\DiscordLogger;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger as Monolog;

class LogHandler extends AbstractProcessingHandler
{
    /** @var string */
    private $appName;

    /** @var string */
    private $appEnv;

    /** @var string */
    private $webHookUrl;

    public function __construct(string $webHookUrl, int $level)
    {
        parent::__construct(Monolog::toMonologLevel($level));

        $this->level = $level;
        $this->webHookUrl = $webHookUrl;

        // define variables for making Telegram request
        $this->botToken = config('telegram-logger.token');
        $this->chatId = config('telegram-logger.chat_id');
        // define variables for text message
        $this->appName = config('app.name');
        $this->appEnv = config('app.env');
    }

    /**
     * @param array $record
     */
    public function write(array $record)
    {
    }
}
