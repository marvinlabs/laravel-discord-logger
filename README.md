# Laravel Discord Logger

[![Latest Version on Packagist](https://img.shields.io/packagist/v/marvinlabs/laravel-discord-logger.svg?style=flat-square)](https://packagist.org/packages/marvinlabs/laravel-discord-logger)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Total Downloads](https://img.shields.io/packagist/dt/marvinlabs/laravel-discord-logger.svg?style=flat-square)](https://packagist.org/packages/marvinlabs/laravel-discord-logger)

`marvinlabs/laravel-discord-logger` is a laravel package providing a logging handler to send logs to a Discord channel. 

## Installation

You can install the package via composer:

``` bash
composer require marvinlabs/laravel-discord-logger
```

If you are using Laravel 5.5 or later, the service provider will automatically be discovered. 

On earlier versions, you need to do that manually. You must install the service provider:

```php
// config/app.php
'providers' => [
    ...
    MarvinLabs\DiscordLogger\ServiceProvider::class
];
```

You can then publish the configuration file:

``` bash
php artisan vendor:publish --provider "MarvinLabs\DiscordLogger\ServiceProvider"
```

## Setup

### Prepare the discord channel web hook

Create a discord web hook for the channel which will receive the logs.

### Prepare the logger configuration

You must add a new channel to your `config/logging.php` file:

```php
// config/logging.php
'channels' => [
    //...
    'discord' => [
        'driver' => 'custom',
        'via'    => MarvinLabs\DiscordLogger\Logger::class,
        'level'  => 'debug',
        'url'    => env('LOG_DISCORD_WEBHOOK_URL'),
    ],
];
```

You can then provide the web-hook URL in your `.env` file:

```
LOG_DISCORD_WEBHOOK_URL=https://discordapp.com/api/webhooks/abcd/1234
```

### Use the logger channel

You have two options: log only to discord or add the channel to the stack

#### Log only to the discord channel

Simply change the `.env` variable to use the discord channel

```
LOG_CHANNEL=discord
```

#### Add the channel on top of other channels

Add the channel to the stack in the `config/logging.php` configuration:

```php
// config/logging.php
'channels' => [
    //...
    'stack' => [
        'driver'   => 'stack',
        'channels' => ['single', 'discord'],
    ],
];
```

Then make sure the logging channel is set to stack in your `.env` file:

```
LOG_CHANNEL=stack
```

#### Logging to multiple Discord channels

Of course, you can send your log messages to multiple Discord channels. Just create as many channels as desired in 
`config/logging.php` and put them in the stack. Each channel should be named differently and should point to a different
web hook URL.

## What does it look like?

You can get a preview of what it looks like using each of the provided converters.

![Screenshot](/assets/screenshot.png)

## Version history

See the [dedicated change log](CHANGELOG.md)

## Credits

- Got some ideas from [GrKamil/laravel-telegram-logging](https://github.com/GrKamil/laravel-telegram-logging)
- Got some ideas from [lefuturiste/monolog-discord-handler](https://github.com/lefuturiste/monolog-discord-handler)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
