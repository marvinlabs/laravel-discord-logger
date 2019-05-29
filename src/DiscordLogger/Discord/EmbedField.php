<?php

namespace MarvinLabs\DiscordLogger\Discord;

use Illuminate\Contracts\Support\Arrayable;

class EmbedField implements Arrayable
{
    /** @var string */
    public $name;

    /** @var string */
    public $value;

    /** @var bool */
    public $inline;

    /** Static factory method */
    public static function make(string $name = '', string $value='', bool $inline = false): EmbedField
    {
        return new self($name, $value, $inline);
    }

    protected function __construct(string $name, string $value, bool $inline = false)
    {
        $this->name = $name;
        $this->value = $value;
        $this->inline = $inline;
    }

    public function name(string $name): EmbedField
    {
        $this->name = $name;
        return $this;
    }

    public function value(string $value): EmbedField
    {
        $this->value = $value;
        return $this;
    }

    public function inline(bool $inline = true): EmbedField
    {
        $this->inline = $inline;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'name'   => $this->name,
            'value'  => $this->value,
            'inline' => $this->inline,
        ];
    }
}
