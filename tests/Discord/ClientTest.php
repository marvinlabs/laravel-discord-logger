<?php

namespace MarvinLabs\DiscordLogger\Tests\Discord;

use GuzzleHttp\Client as HttpClient;
use MarvinLabs\DiscordLogger\Discord\Client;
use MarvinLabs\DiscordLogger\Discord\Message;
use MarvinLabs\DiscordLogger\Tests\TestCase;

class ClientTest extends TestCase
{
    /** @var \MarvinLabs\DiscordLogger\Discord\Client */
    private $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = new Client(new HttpClient(), config('logging.channels.discord.url'));
    }

    /** @test */
    public function can_send_a_simple_message()
    {
        $this->client->send(Message::make('This is a test'));
        $this->expectNotToPerformAssertions();
    }
}
