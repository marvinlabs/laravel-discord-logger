<?php


namespace MarvinLabs\DiscordLogger\Tests;


use MarvinLabs\DiscordLogger\Contracts\DiscordWebHook;

class ServiceProviderTest extends TestCase
{
    /** @test */
    public function can_provide_discord_webhook_instance()
    {
        $discord = $this->app->makeWith(DiscordWebHook::class, ['url' => 'http://example.com']);

        $this->assertNotNull($discord);
        $this->assertEquals('http://example.com', $discord->getUrl());
    }
}
