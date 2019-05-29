<?php

namespace MarvinLabs\DiscordLogger\Discord\Exceptions;

use Exception;
use GuzzleHttp\Exception\ClientException;
use Throwable;

class MessageCouldNotBeSent extends Exception
{
    /** Thrown when a 4xx http error code is received. */
    public static function serviceRespondedWithAnError(ClientException $exception): MessageCouldNotBeSent
    {
        $response = $exception->getResponse();
        $code = $response===null ? 0 : $response->getStatusCode();
        $message = $response===null ? '' : $response->getBody()->getContents();

        return new static("Discord web hook responded with an error ({$code}): {$message}");
    }

    /** Thrown when the api is not reachable. */
    public static function couldNotCommunicateWithDiscord(Throwable $e): MessageCouldNotBeSent
    {
        return new static('The communication with Discord Web hook failed.', $e->getCode(), $e);
    }
}
