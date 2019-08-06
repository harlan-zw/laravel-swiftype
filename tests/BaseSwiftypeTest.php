<?php

namespace Loonpwn\Swiftype\Tests;

use Loonpwn\Swiftype\Facades\Swiftype;
use Loonpwn\Swiftype\Facades\SwiftypeEngine;

class BaseSwiftypeTest extends \Orchestra\Testbench\TestCase
{
    protected function getPackageAliases($app)
    {
        return [
            'Swiftype' => 'Loonpwn\Swiftype\Api',
            'SwiftypeEngine' => 'Loonpwn\Swiftype\Engine',
        ];
    }

    protected function getPackageProviders($app)
    {
        return ['Loonpwn\Swiftype\SwiftypeServiceProvider'];
    }

    public function log(...$message) {
        echo implode(', ', $message) . "\n";
    }
}
