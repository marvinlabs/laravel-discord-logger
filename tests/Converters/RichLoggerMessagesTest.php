<?php

namespace MarvinLabs\DiscordLogger\Tests\Converters;

use DateTime;
use Exception;
use MarvinLabs\DiscordLogger\Contracts\RecordToMessage;
use MarvinLabs\DiscordLogger\Converters\RichRecordConverter;
use MarvinLabs\DiscordLogger\Converters\SimpleRecordConverter;
use MarvinLabs\DiscordLogger\Tests\Support\MessageAssertions;

class RichLoggerMessagesTest extends AbstractLoggerMessagesTest
{
    protected function getConverter(): RecordToMessage
    {
        return new RichRecordConverter($this->config);
    }

    /** @test */
    public function sends_a_simple_message_for_log()
    {
        $this->config->set('discord-logger.emojis.WARNING', ':poop:');

        $message = $this->warning('This is a test')[0];

        MessageAssertions::assertMessagePartialMatch([
            'content' => ":poop: `[2000-01-01 12:13:14] Laravel.WARNING: This is a test [] []\n`",
        ], $message);
    }
}
