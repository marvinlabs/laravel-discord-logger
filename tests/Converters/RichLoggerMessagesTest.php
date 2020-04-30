<?php

namespace MarvinLabs\DiscordLogger\Tests\Converters;

use Exception;
use MarvinLabs\DiscordLogger\Contracts\RecordToMessage;
use MarvinLabs\DiscordLogger\Converters\RichRecordConverter;
use MarvinLabs\DiscordLogger\Tests\Support\MessageAssertions;
use function json_encode;
use const JSON_PRETTY_PRINT;

class RichLoggerMessagesTest extends AbstractLoggerMessagesTest
{
    protected function getConverter(): RecordToMessage
    {
        return new RichRecordConverter($this->config);
    }

    /** @test */
    public function sends_a_simple_message_for_log()
    {
        $this->config->set('discord-logger.colors.WARNING', 0x123456);
        $this->config->set('discord-logger.emojis.WARNING', ':poop:');

        $message = $this->warning('This is a test', ['foo' => 'bar'], [1, 2, 3, 'four'])[0];

        MessageAssertions::assertMessagePartialMatch([
            'embeds' => [
                0 => ['title'       => ':poop: `[2000-01-01 12:13:14] Laravel.WARNING`',
                      'description' => ':black_small_square: `This is a test`',
                      'color'       => 0x123456],
                1 => ['description' => "**Context**\n`" . json_encode(['foo' => 'bar'], JSON_PRETTY_PRINT) . '`',
                      'color'       => 0x123456,],
                2 => ['description' => "**Extra**\n`" . json_encode([1, 2, 3, 'four'], JSON_PRETTY_PRINT) . '`',
                      'color'       => 0x123456,],
            ],
        ], $message);
    }

    /** @test */
    public function includes_stacktrace_in_content_when_attachment_disabled()
    {
        $this->config->set('discord-logger.stacktrace', 'inline');

        $exception = new Exception();
        $message =   $this->exception('This is a test', $exception)[0];

        $this->assertStringContainsString('[2000-01-01 12:13:14] Laravel.CRITICAL', $message->embeds[0]->title);
        $this->assertStringContainsString('This is a test', $message->embeds[0]->description);

        $this->assertStringContainsString($exception->getTraceAsString(), $message->embeds[1]->description);
    }

    /** @test */
    public function includes_stacktrace_as_file_when_attachment_enabled()
    {
        $this->config->set('discord-logger.emojis.CRITICAL', null);
        $this->config->set('discord-logger.stacktrace', 'file');

        $exception = new Exception();
        $messages = $this->exception('This is a test', $exception);

        MessageAssertions::assertMessagePartialMatch([
            'embeds' => [
                0 => ['title'       => '`[2000-01-01 12:13:14] Laravel.CRITICAL`',
                      'description' => '`This is a test`'],
            ],
        ], $messages[0]);

        MessageAssertions::assertMessagePartialMatch([
            'file' => ['filename' => '20000101121314_stacktrace.txt',
                       'contents' => $exception->getTraceAsString(),],
        ], $messages[1]);
    }
}
