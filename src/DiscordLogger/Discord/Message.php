<?php

namespace MarvinLabs\DiscordLogger\Discord;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Str;
use MarvinLabs\DiscordLogger\Contracts\DiscordWebHook;

class Message implements Arrayable
{
    /** @var string (up to 2000 characters) */
    public $content;

    /** @var string|null */
    public $username;

    /** @var string|null */
    public $avatarUrl;

    /** @var bool */
    public $tts = false;

    /** @var array */
    public $file;

    /** @var array Array of \MarvinLabs\DiscordLogger\Discord\Embed objects */
    public $embeds;

    /** Static factory method */
    public static function make(?string $content = null): Message
    {
        return new self($content);
    }

    protected function __construct(?string $content = null)
    {
        if ($content !== null)
        {
            $this->content($content);
        }
    }

    public function content(string $content): Message
    {
        $this->content = Str::limit($content, DiscordWebHook::MAX_CONTENT_LENGTH - 3 /* Accounting for ellipsis */);
        return $this;
    }

    public function from(string $username, ?string $avatarUrl = null): Message
    {
        $this->username = $username;
        if ($avatarUrl !== null)
        {
            $this->avatarUrl = $avatarUrl;
        }
        return $this;
    }

    public function tts(bool $enabled = true): Message
    {
        $this->tts = $enabled;
        return $this;
    }

    public function file(string $contents, string $filename): Message
    {
        $this->file = [
            'name'     => 'file',
            'contents' => $contents,
            'filename' => $filename,
        ];
        return $this;
    }

    public function embed(Embed $embed): Message
    {
        $this->embeds[] = $embed;
        return $this;
    }

    public function toArray(): array
    {
        return array_filter(
            ['content'    => $this->content,
             'username'   => $this->username,
             'avatar_url' => $this->avatarUrl,
             'tts'        => $this->tts ? 'true' : 'false',
             'file'       => $this->file,
             'embeds'     => $this->serializeEmbeds(),],
            static function ($value) {
                return $value !== null && $value !== [];
            });
    }

    protected function serializeEmbeds(): array
    {
        return array_map(static function (Arrayable $embed) {
            return $embed->toArray();
        }, $this->embeds ?? []);
    }
}
