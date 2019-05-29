<?php

namespace MarvinLabs\DiscordLogger;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /** @return void */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/discord-logger.php', 'discord-logger');
    }

    /** @return void */
    public function boot()
    {
        $this->publishes([__DIR__ . '/../../config/discord-logger.php' => config_path('discord-logger.php')], 'config');
    }
}
