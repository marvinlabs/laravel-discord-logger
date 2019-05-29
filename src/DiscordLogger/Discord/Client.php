<?php

namespace MarvinLabs\DiscordLogger\Discord;

use Exception;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\ClientException;
use MarvinLabs\DiscordLogger\Discord\Exceptions\InvalidMessage;
use MarvinLabs\DiscordLogger\Discord\Exceptions\MessageCouldNotBeSent;

class Client
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

    /**
     * @throws \MarvinLabs\DiscordLogger\Discord\Exceptions\InvalidMessage
     * @throws \MarvinLabs\DiscordLogger\Discord\Exceptions\MessageCouldNotBeSent
     */
    public function send(Message $message): void
    {
        $payload = $this->buildPayload($message);

        try
        {
            $this->http->post($this->url, ['json' => $payload]);
        }
        catch (ClientException $e)
        {
            throw MessageCouldNotBeSent::serviceRespondedWithAnError($e);
        }
        catch (Exception $e)
        {
            throw MessageCouldNotBeSent::couldNotCommunicateWithDiscord($e->getMessage());
        }
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

        return $message->toArray();
    }

    protected function isMessageEmpty($message): bool
    {
        return $message->content === null
               && $message->file === null
               && $message->embeds === null;
    }
}
