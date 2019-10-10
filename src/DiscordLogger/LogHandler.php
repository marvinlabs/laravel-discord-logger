<?php

namespace MarvinLabs\DiscordLogger;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Container\Container;
use MarvinLabs\DiscordLogger\Contracts\DiscordWebHook;
use MarvinLabs\DiscordLogger\Contracts\RecordToMessage;
use MarvinLabs\DiscordLogger\Converters\SimpleRecordConverter;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger as Monolog;
use RuntimeException;
use function class_implements;

class LogHandler extends AbstractProcessingHandler
{
    /** @var \MarvinLabs\DiscordLogger\Contracts\DiscordWebHook */
    private $discord;

    /** @var \MarvinLabs\DiscordLogger\Contracts\RecordToMessage */
    private $recordToMessage;

    /** @throws \Illuminate\Contracts\Container\BindingResolutionException */
    public function __construct(Container $container, Repository $config, array $channelConfig)
    {
        parent::__construct(Monolog::toMonologLevel($channelConfig['level'] ?? Monolog::DEBUG));

        $this->discord = $container->make(DiscordWebHook::class, ['url' => $channelConfig['url']]);
        $this->recordToMessage = $this->createRecordConverter($container, $config);
    }

    public function write(array $record): void
    {
        foreach($this->recordToMessage->buildMessages($record) as $message)
        {
            $this->discord->send($message);
        }
    }

    /** @throws \Illuminate\Contracts\Container\BindingResolutionException */
    protected function createRecordConverter(Container $container, Repository $config): RecordToMessage
    {
        $converter = $container->make(
            $config->get('discord-logger.converter', SimpleRecordConverter::class));

        if (!class_implements($converter, RecordToMessage::class))
        {
            throw new RuntimeException('The converter specified in the discord-logger configuration should implement the RecordToMessage interface');
        }

        return $converter;
    }

}
