<?php

namespace MarvinLabs\DiscordLogger\Discord\Exceptions;

use Exception;

class InvalidMessage extends Exception
{
    /**
     * Thrown when a file upload contents an embedded content.
     * Because uploading files require a multipart/form-data request.
     */
    public static function embedsNotSupportedWithFileUploads(): InvalidMessage
    {
        return new static('Embedded Content is not supported with File Uploads.');
    }

    /**
     * Thrown when the message does not contain a content, file or message.
     */
    public static function cannotSendAnEmptyMessage(): InvalidMessage
    {
        return new static('Cannot send an empty message');
    }
}
