<?php

namespace MarvinLabs\DiscordLogger\Tests;

use DateTime;
use InvalidArgumentException;
use MarvinLabs\DiscordLogger\Contracts\DiscordWebHook;
use MarvinLabs\DiscordLogger\Logger;
use MarvinLabs\DiscordLogger\Tests\Support\FakeDiscordWebHook;

class LoggerTest extends TestCase
{

    /** @var \MarvinLabs\DiscordLogger\Tests\Support\FakeDiscordWebHook */
    private $discordFake;

    /** @var \MarvinLabs\DiscordLogger\Logger */
    private $logger;

    /** @var \Monolog\Logger */
    private $monolog;

    protected function setUp(): void
    {
        parent::setUp();

        $this->discordFake = new FakeDiscordWebHook('http://example.com');
        $this->app->bind(DiscordWebHook::class, function () {
            return $this->discordFake;
        });

        $this->logger = $this->app->make(Logger::class);
        $this->monolog = ($this->logger)(['level' => 'INFO', 'url' => 'http://example.com']);
    }

    /** @test */
    public function throws_exception_if_url_missing_from_channel_configuration()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('You must set the `url` key in your discord channel configuration');

        $this->logger([]);
    }

    /** @test */
    public function log_is_sent_to_discord()
    {
        $this->monolog->warn('This is a test');
        $this->discordFake->assertSendCount(1);
    }

    /** @test */
    public function message_is_sent_with_expected_from_fields()
    {
        $this->app['config']->set('discord-logger.from.name', 'John');
        $this->app['config']->set('discord-logger.from.avatar_url', 'http://example.com/avatar.png');

        $this->monolog->warn('This is a test');

        $this->discordFake->assertLastMessageMatches([
            'username'   => 'John',
            'avatar_url' => 'http://example.com/avatar.png',
        ]);
    }

    /** @test */
    public function message_content_contains_date_and_level()
    {
        $this->app['config']->set('app.name', 'my-app');
        $now = (new DateTime())->format('Y-m-d H:i:s');

        $this->monolog->warn('This is a test');

        $this->discordFake->assertLastMessageMatches([
            'content'   => "[$now] my-app.WARNING",
        ]);
    }

    /** @test */
    public function message_contains_an_embed_for_the_log_content()
    {
        $this->monolog->warn('This is a test');

        $this->discordFake->assertLastMessageMatches([
            'embeds'   => [
                'title' => 'wer',
            ],
        ]);
    }
}
