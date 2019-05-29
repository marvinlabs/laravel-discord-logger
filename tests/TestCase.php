<?php

namespace MarvinLabs\DiscordLogger\Tests;

use MarvinLabs\DiscordLogger\ServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app)
    {
        return [ServiceProvider::class,];
    }

    protected function getPackageAliases($app)
    {
        return [];
    }
}
