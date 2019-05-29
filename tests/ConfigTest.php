<?php

namespace MarvinLabs\DiscordLogger\Tests;

use Monolog\Logger;
use function config;

class ConfigTest extends TestCase
{
    /** @test */
    public function colors_cover_all_log_levels()
    {
        $colors = config('discord-logger.colors');

        collect(Logger::getLevels())
            ->map(static function ($level) {
                return Logger::getLevelName($level);
            })
            ->each(function ($level) use ($colors) {
                $this->assertArrayHasKey($level, $colors, "Color for level $level is not configured");
            });
    }

    /** @test */
    public function emojis_cover_all_log_levels()
    {
        $colors = config('discord-logger.emojis');

        collect(Logger::getLevels())
            ->map(static function ($level) {
                return Logger::getLevelName($level);
            })
            ->each(function ($level) use ($colors) {
                $this->assertArrayHasKey($level, $colors, "Emoji for level $level is not configured");
            });
    }
}
