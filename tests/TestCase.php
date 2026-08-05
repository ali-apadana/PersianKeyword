<?php

declare(strict_types=1);

namespace PersianKeyword\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use PersianKeyword\PersianKeywordServiceProvider;

abstract class TestCase extends Orchestra
{
    /** @return list<class-string> */
    protected function getPackageProviders($app): array
    {
        return [PersianKeywordServiceProvider::class];
    }
}
