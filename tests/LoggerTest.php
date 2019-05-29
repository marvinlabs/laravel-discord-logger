<?php

namespace MarvinLabs\DiscordLogger\Tests;

use InvalidArgumentException;
use MarvinLabs\DiscordLogger\Logger;

class LoggerTest extends TestCase
{
    /** @test */
    public function throws_exception_if_url_missing_from_channel_configuration()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('You must set the `url` key in your discord channel configuration');

        $logger = $this->app->make(Logger::class);
        $logger([]);
    }
}
