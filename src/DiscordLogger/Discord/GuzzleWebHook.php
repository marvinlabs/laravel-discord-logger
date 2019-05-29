<?php

namespace MarvinLabs\DiscordLogger\Discord;

use Exception;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\ClientException;
use MarvinLabs\DiscordLogger\Contracts\DiscordWebHook;
use MarvinLabs\DiscordLogger\Discord\Exceptions\InvalidMessage;
use MarvinLabs\DiscordLogger\Discord\Exceptions\MessageCouldNotBeSent;

class GuzzleWebHook implements DiscordWebHook
{
    /** @var \GuzzleHttp\Client */
    protected $http;

    /** @var string */
    protected $url;

    public function __construct(HttpClient $http, string $url)
    {
        $this->http = $http;
        $this->url = $url;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @throws \MarvinLabs\DiscordLogger\Discord\Exceptions\InvalidMessage
     * @throws \MarvinLabs\DiscordLogger\Discord\Exceptions\MessageCouldNotBeSent
     */
    public function send(Message $message): void
    {
        $payload = $this->buildPayload($message);
        $requestType = $this->requestType($message);

        try
        {
            $this->http->post($this->url, [$requestType => $payload]);
        }
        catch (ClientException $e)
        {
            throw MessageCouldNotBeSent::serviceRespondedWithAnError($e);
        }
        catch (Exception $e)
        {
            throw MessageCouldNotBeSent::couldNotCommunicateWithDiscord($e);
        }
    }

    protected function requestType(Message $message): string
    {
        return $message->file !== null ? 'multipart' : 'json';
    }

    /**
     * @throws \MarvinLabs\DiscordLogger\Discord\Exceptions\InvalidMessage
     */
    protected function buildPayload(Message $message): array
    {
        if ($this->isMessageEmpty($message))
        {
            throw InvalidMessage::cannotSendAnEmptyMessage();
        }

        if ($this->requestType($message) === 'multipart')
        {
            return $this->buildMultipartPayload($message);
        }

        return $this->buildJsonPayload($message);
    }

    /**
     * @throws \MarvinLabs\DiscordLogger\Discord\Exceptions\InvalidMessage
     */
    protected function buildJsonPayload(Message $message): array
    {
        if ($this->isMessageEmpty($message))
        {
            throw InvalidMessage::cannotSendAnEmptyMessage();
        }

        return collect($message->toArray())->forget('file')->all();
    }

    /**
     * @throws \MarvinLabs\DiscordLogger\Discord\Exceptions\InvalidMessage
     */
    protected function buildMultipartPayload(Message $message): array
    {
        if ($message->embeds !== null)
        {
            throw InvalidMessage::embedsNotSupportedWithFileUploads();
        }

        return collect($message->toArray())
            ->forget('file')
            ->reject(static function ($value) {
                return $value === null;
            })
            ->map(static function ($value, $key) {
                return ['name' => $key, 'contents' => $value];
            })
            ->push($message->file)
            ->values()
            ->all();
    }

    protected function isMessageEmpty($message): bool
    {
        return $message->content === null
               && $message->file === null
               && $message->embeds === null;
    }
}
