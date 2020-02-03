<?php

namespace Loonpwn\Swiftype\Tests;

use Illuminate\Support\Collection;
use Loonpwn\Swiftype\Clients\Api;
use Loonpwn\Swiftype\Clients\Engine;
use Loonpwn\Swiftype\Tests\App\Models\User;

class BaseTestCase extends \Orchestra\Testbench\TestCase
{
    /**
     * @var \Elastic\AppSearch\Client\Client
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

        $this->withFactories(__DIR__.'/database/factories');

        $this->client = app(Api::class);
        $this->engine = app(Engine::class);

        $this->engine->purgeAllDocuments();

        $this->engine->updateSchema([
            'name' => 'text',
            'email' => 'text',
        ]);
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
        $app['config']->set('scout.driver', 'swiftype');
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
        return factory(User::class)
            ->times($count)
            ->create();
    }

    public function log(...$message)
    {
        echo implode(', ', $message)."\n";
    }
}
