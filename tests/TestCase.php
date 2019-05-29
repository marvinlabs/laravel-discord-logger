<?php

namespace MarvinLabs\DiscordLogger\Tests;

use MarvinLabs\DiscordLogger\ServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        tap($this->app['config']->get('logging.channels'), function ($channels) {
            $channels['discord'] = ['driver' => 'custom',
                                    'via'    => Logger::class,
                                    'level'  => 'debug',
                                    'url'    => env('LOG_DISCORD_WEBHOOK_URL', null),];
            $this->app['config']->set('logging.channels', $channels);
        });
    }

    protected function getPackageProviders($app)
    {
        return [ServiceProvider::class,];
    }

    protected function getPackageAliases($app)
    {
        return [];
    }
}
