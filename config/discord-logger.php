<?php

return [

    /*
     * The author of the log messages. You can set both to null to keep the Webhook author set in Discord
     */
    'from'       => [
        'name'       => env('APP_NAME', 'Discord Logger'),
        'avatar_url' => null,
    ],

    /**
     * The converter to use to turn a log record into a discord message
     *
     * Bundled converters:
     * - \MarvinLabs\DiscordLogger\Converters\SimpleRecordConverter::class
     * - \MarvinLabs\DiscordLogger\Converters\RichRecordConverter::class
     */
    'converter'  => \MarvinLabs\DiscordLogger\Converters\RichRecordConverter::class,

    /**
     * If enabled, stacktraces will be attached as files. If not, stacktraces will be directly printed out in the
     * message.
     *
     * Valid values are:
     *
     * - 'smart': when stacktrace is less than 2000 characters, it is inlined with the message, else attached as file
     * - 'file': stacktrace is always attached as file
     * - 'inline': stacktrace is always inlined with the message, truncated if necessary
     */
    'stacktrace' => 'smart',

    /*
     * A set of colors to associate to the different log levels when using the `RichRecordConverter`
     */
    'colors'     => [
        'DEBUG'     => 0x607d8b,
        'INFO'      => 0x4caf50,
        'NOTICE'    => 0x2196f3,
        'WARNING'   => 0xff9800,
        'ERROR'     => 0xf44336,
        'CRITICAL'  => 0xe91e63,
        'ALERT'     => 0x673ab7,
        'EMERGENCY' => 0x9c27b0,
    ],

    /*
     * A set of emojis to associate to the different log levels. Set to null to disable an emoji for a given level
     */
    'emojis'     => [
        'DEBUG'     => ':beetle:',
        'INFO'      => ':bulb:',
        'NOTICE'    => ':wink:',
        'WARNING'   => ':flushed:',
        'ERROR'     => ':poop:',
        'CRITICAL'  => ':imp:',
        'ALERT'     => ':japanese_ogre:',
        'EMERGENCY' => ':skull:',
    ],
];
