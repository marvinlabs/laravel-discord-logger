<?php

namespace MarvinLabs\DiscordLogger;

use Illuminate\Config\Repository;
use Illuminate\Contracts\Container\Container;
use MarvinLabs\DiscordLogger\Contracts\DiscordWebHook;
use Monolog\Logger as Monolog;

class Logger
{
    /** @var \Illuminate\Config\Repository */
    private $config;
    /** @var \Illuminate\Contracts\Container\Container */
    private $container;

    public function __construct(Container $container, Repository $config)
    {
        $this->config = $config;
        $this->container = $container;
    }

    public function __invoke(array $config)
    {
        $discord = $this->container->make(DiscordWebHook::class, [$config['webhook_url']]);

        return new Monolog($this->config->get('app.name'), [
            new LogHandler($this->config, $discord, $config['level'])]);
    }
}
