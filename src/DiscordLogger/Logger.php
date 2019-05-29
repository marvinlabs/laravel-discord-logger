<?php

namespace MarvinLabs\DiscordLogger;

use Monolog\Logger as Monolog;

/**
 * Class TelegramLogger
 *
 * @package App\Logging
 */
class Logger
{
    /**
     * Create a custom Monolog instance.
     *
     * @param array $config
     * @return \Monolog\Logger
     */
    public function __invoke(array $config)
    {
        return new Monolog(config('app.name'), [new LogHandler($config['webhook_url'], $config['level'])]);
    }
}
