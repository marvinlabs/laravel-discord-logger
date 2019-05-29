<?php

namespace MarvinLabs\DiscordLogger\Tests\Discord;

use Illuminate\Support\Str;
use MarvinLabs\DiscordLogger\Contracts\DiscordWebHook;
use MarvinLabs\DiscordLogger\Discord\Embed;
use MarvinLabs\DiscordLogger\Tests\TestCase;
use function strlen;

class EmbedTest extends TestCase
{
    /** @test */
    public function description_is_truncated_to_2000_characters()
    {
        $longString = Str::random(DiscordWebHook::MAX_CONTENT_LENGTH + 500);

        $embed = Embed::make()->description($longString);

        $this->assertLessThanOrEqual(DiscordWebHook::MAX_CONTENT_LENGTH, strlen($embed->description));
    }

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

        $this->assertEquals(
            ['title'       => 'my title',
             'description' => 'my description',
             'url'         => 'main.url',
             'color'       => 0x123456,
             'footer'      => ['text'     => 'my footer',
                               'icon_url' => 'footer-icon.url',],
             'image'       => ['url' => 'image.url'],
             'thumbnail'   => ['url' => 'thumbnail.url'],
             'author'      => ['name'     => 'John',
                               'url'      => 'avatar.url',
                               'icon_url' => 'author-icon.url',],
             'fields'      => [['name'   => 'first-field',
                                'value'  => 'foo',
                                'inline' => true,],
                               ['name'   => 'second-field',
                                'value'  => 'bar',
                                'inline' => false,],],
            ],
            $embed->toArray());
    }
}
