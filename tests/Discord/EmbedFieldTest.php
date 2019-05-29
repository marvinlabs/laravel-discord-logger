<?php

namespace MarvinLabs\DiscordLogger\Tests\Discord;

use MarvinLabs\DiscordLogger\Discord\EmbedField;
use MarvinLabs\DiscordLogger\Tests\TestCase;

class EmbedFieldTest extends TestCase
{
    /** @test */
    public function can_convert_to_array()
    {
        $field = EmbedField::make()->name('foo')->value('bar')->inline(true);

        $this->assertEquals(
            ['name' => 'foo', 'value' => 'bar', 'inline' => true,],
            $field->toArray());
    }
}
