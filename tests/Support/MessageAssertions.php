<?php


namespace MarvinLabs\DiscordLogger\Tests\Support;


use MarvinLabs\DiscordLogger\Discord\Message;
use PHPUnit\Framework\Assert;

class MessageAssertions
{

    public static function assertMessageEquals(Message $expected, Message $actual)
    {
        Assert::assertEquals($expected->toArray(), $actual->toArray());
    }

    public static function assertMessagePartialMatch(array $expectedSubset, Message $actual)
    {
        self::assertArraySubset($actual->toArray(), $expectedSubset);
    }

    protected static function assertArraySubset(array $actualArray, array $expectedSubset)
    {
        foreach ($expectedSubset as $key => $value)
        {
            if (is_array($value))
            {
                self::assertArraySubset($actualArray[$key], $value);
                continue;
            }

            Assert::assertArrayHasKey($key, $actualArray);
            Assert::assertSame($value, $actualArray[$key]);
        }
    }
}
