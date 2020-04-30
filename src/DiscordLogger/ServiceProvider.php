<?php

namespace MarvinLabs\DiscordLogger;

use GuzzleHttp\Client;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use MarvinLabs\DiscordLogger\Contracts\DiscordWebHook;
use MarvinLabs\DiscordLogger\Discord\GuzzleWebHook;
use Illuminate\Support\Str;

class ServiceProvider extends BaseServiceProvider
{
    /** @return void */
    public function register()
    {
        if (!Str::contains($this->app->version(), 'Lumen')) {
            $this->mergeConfigFrom(__DIR__ . '/../../config/discord-logger.php', 'discord-logger');
        }
        $this->registerContainerBindings();
    }

    /** @return void */
    public function boot()
    {
        if (Str::contains($this->app->version(), 'Lumen')) {
            $this->app->configure('discord-logger');
        } else {
            $this->publishes([__DIR__ . '/../../config/discord-logger.php' => config_path('discord-logger.php')], 'config');
        }
    }

    protected function registerContainerBindings(): void
    {
        $this->app->bind(DiscordWebHook::class, static function (Container $app, $params) {
            if (empty($params['url']))
            {
                throw new BindingResolutionException('You must provide a URL to make a DiscordWebHook instance');
            }

            $guzzle = $app->bound(Client::class) ? $app->make(Client::class) : new Client();
            return new GuzzleWebHook($guzzle, $params['url']);
        });
    }
}
