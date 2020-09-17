<?php

namespace Loonpwn\Swiftype\Tests;

use Illuminate\Support\Collection;
use Loonpwn\Swiftype\Clients\Api;
use Loonpwn\Swiftype\Clients\Engine;
use Loonpwn\Swiftype\Facades\Swiftype;
use Loonpwn\Swiftype\Facades\SwiftypeEngine;
use Loonpwn\Swiftype\Tests\App\Models\User;

class BaseTestCase extends \Orchestra\Testbench\TestCase
{
    /**
     * @var Api
     */
    public $client;

    /**
     * @var Engine
     */
    public $engine;

    public function setUp(): void
    {
        parent::setUp();

        $this->loadLaravelMigrations();

        /* @var Api $client */
        $this->client = app(Swiftype::class);
        $this->engine = app(SwiftypeEngine::class);

        // make sure there are no documents within
        $this->engine->purgeAllDocuments();
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('swiftype.sync_models', [
            User::class,
        ]);
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }

    protected function getPackageAliases($app)
    {
        return [
            'Swiftype' => 'Loonpwn\Swiftype\Clients\Api',
            'Engine' => 'Loonpwn\Swiftype\Clients\Engine',
        ];
    }

    protected function getPackageProviders($app)
    {
        return [
            'Loonpwn\Swiftype\SwiftypeServiceProvider',
        ];
    }

    /**
     * @param int $count
     * @return Collection
     */
    protected function indexSeedDocuments($count = 5)
    {
        return User::factory()->count($count)->create();
    }

    public function log(...$message)
    {
        echo implode(', ', $message)."\n";
    }
}
