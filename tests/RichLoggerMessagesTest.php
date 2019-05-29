<?php

namespace MarvinLabs\DiscordLogger\Tests;

use DateTime;
use RuntimeException;

class RichLoggerMessagesTest extends AbstractLoggerMessagesTest
{

    /** @test */
    public function message_content_contains_date_and_level()
    {
        $now = (new DateTime())->format('Y-m-d H:i:s');

        $this->monolog->warn('This is a test');

        $this->discordFake->assertLastMessageMatches([
            'content' => "[$now] Laravel.WARNING",
        ]);
    }

    /** @test */
    public function message_contains_an_embed_for_the_log_content()
    {
        $this->monolog->warn('This is a test');

        $this->discordFake->assertLastMessageMatches([
            'embeds' => [0 => [
                'description' => '`This is a test`',
                'color'       => $this->config->get('discord-logger.colors.WARNING'),
            ]],
        ]);
    }

    /** @test */
    public function stacktrace_is_in_an_extra_embed_when_provided()
    {
        $exception = new RuntimeException();
        $this->monolog->error('This is an exception', ['exception' => $exception]);

        $this->discordFake->assertLastMessageMatches([
            'embeds' => [1 => [
                'description' => "`{$exception->getTraceAsString()}`",
                'color'       => $this->config->get('discord-logger.colors.ERROR'),
            ]],
        ]);
    }
}
