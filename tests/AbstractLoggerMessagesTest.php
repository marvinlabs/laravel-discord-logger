<?php

namespace MarvinLabs\DiscordLogger\Tests;

use MarvinLabs\DiscordLogger\Contracts\DiscordWebHook;
use MarvinLabs\DiscordLogger\Logger;
use MarvinLabs\DiscordLogger\Tests\Support\FakeDiscordWebHook;

abstract class AbstractLoggerMessagesTest extends TestCase
{

    /** @var \MarvinLabs\DiscordLogger\Tests\Support\FakeDiscordWebHook */
    protected $discordFake;

    /** @var \MarvinLabs\DiscordLogger\Logger */
    protected $logger;

    /** @var \Monolog\Logger */
    protected $monolog;

    /** @var \Illuminate\Contracts\Config\Repository */
    protected $config;

    protected function setUp(): void
    {
        parent::setUp();

        $this->discordFake = new FakeDiscordWebHook('http://example.com');
        $this->app->bind(DiscordWebHook::class, function () {
            return $this->discordFake;
        });

        $this->logger = $this->app->make(Logger::class);
        $this->monolog = ($this->logger)(['level' => 'INFO', 'url' => 'http://example.com']);

        $this->config = $this->app['config'];
    }

    /** @test */
    public function message_is_sent_with_expected_from_fields()
    {
        $this->config->set('discord-logger.from.name', 'John');
        $this->config->set('discord-logger.from.avatar_url', 'http://example.com/avatar.png');

        $this->monolog->warn('This is a test');

        $this->discordFake->assertLastMessageMatches([
            'username'   => 'John',
            'avatar_url' => 'http://example.com/avatar.png',
        ]);
    }
}
