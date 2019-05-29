<?php

namespace MarvinLabs\DiscordLogger\Tests;

use DateTime;
use Exception;

class SimpleLoggerMessagesTest extends AbstractLoggerMessagesTest
{
    /** @test */
    public function sends_a_simple_message_for_log()
    {
        $this->config->set('discord-logger.emojis.WARNING', ':poop:');
        $now = (new DateTime())->format('Y-m-d H:i:s');

        $this->monolog->warn('This is a test');

        $this->discordFake->assertLastMessageMatches([
            'content' => ":poop: `[$now] Laravel.WARNING: This is a test [] []\n`",
        ]);
    }

    /** @test */
    public function does_not_include_emoji_if_disabled()
    {
        $this->config->set('discord-logger.emojis.WARNING', null);
        $now = (new DateTime())->format('Y-m-d H:i:s');

        $this->monolog->warn('This is a test');

        $this->discordFake->assertLastMessageMatches([
            'content' => "`[$now] Laravel.WARNING: This is a test [] []\n`",
        ]);
    }

    /** @test */
    public function includes_stacktrace_in_content_when_attachment_disabled()
    {
        $this->config->set('discord-logger.stacktrace', 'inline');

        $now = (new DateTime())->format('Y-m-d H:i:s');
        $exception = new Exception();

        $this->monolog->warn('This is a test', ['exception' => $exception]);

        $actualContent = $this->discordFake->getLastMessageSent()->content;
        $this->assertStringContainsString("[$now] Laravel.WARNING: This is a test", $actualContent);
        $this->assertStringContainsString($exception->getTraceAsString(), $actualContent);
    }

    /** @test */
    public function includes_stacktrace_as_file_when_attachment_enabled()
    {
        $this->config->set('discord-logger.stacktrace', 'file');
        $now = (new DateTime())->format('Y-m-d H:i:s');
        $timestamp = (new DateTime())->format('YmdHis');
        $exception = new Exception();

        $this->monolog->warn('This is a test', ['exception' => $exception]);

        $actualContent = $this->discordFake->getLastMessageSent()->content;
        $this->assertStringContainsString("[$now] Laravel.WARNING: This is a test", $actualContent);

        $this->discordFake->assertLastMessageMatches([
            'file' => ['filename' => "{$timestamp}_stacktrace.txt",
                       'contents' => $exception->getTraceAsString(),],
        ]);
    }
}
