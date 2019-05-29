<?php

namespace MarvinLabs\DiscordLogger\Tests\Discord;

use MarvinLabs\DiscordLogger\Discord\Embed;
use MarvinLabs\DiscordLogger\Discord\Message;
use MarvinLabs\DiscordLogger\Tests\TestCase;

class MessageTest extends TestCase
{
    /** @test */
    public function can_convert_to_array()
    {
        $embed = Embed::make()
            ->title('my title', 'main.url')
            ->author('John', 'avatar.url', 'author-icon.url')
            ->description('my description')
            ->color(0x123456)
            ->image('image.url')
            ->thumbnail('thumbnail.url')
            ->field('first-field', 'foo', true)
            ->field('second-field', 'bar', false)
            ->footer('my footer', 'footer-icon.url');

        $message = Message::make()
            ->content('my content')
            ->from('John', 'avatar.url')
            ->tts()
            ->embed($embed)
            ->file('file content', 'example.txt');

        $this->assertEquals(
            ['content'    => 'my content',
             'username'   => 'John',
             'avatar_url' => 'avatar.url',
             'tts'        => true,
             'file'       => ['name'     => 'file',
                              'contents' => 'file content',
                              'filename' => 'example.txt',],
             'embeds'     => [$embed->toArray(),],
            ],
            $message->toArray());
    }

    /** @test */
    public function can_convert_message_without_embeds_to_array()
    {
        $message = Message::make()
            ->content('my content')
            ->from('John', 'avatar.url')
            ->tts(false)
            ->file('file content', 'example.txt');

        $this->assertEquals(
            ['content'    => 'my content',
             'username'   => 'John',
             'avatar_url' => 'avatar.url',
             'tts'        => false,
             'file'       => ['name'     => 'file',
                              'contents' => 'file content',
                              'filename' => 'example.txt',],
             'embeds'     => [],
            ],
            $message->toArray());
    }
}
