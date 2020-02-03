<?php

namespace Loonpwn\Swiftype\Console\Commands;

use Illuminate\Console\Command;
use Loonpwn\Swiftype\Clients\Engine;

class SyncSchema extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'swiftype:schema';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends an api request to swiftype to update the schema';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Get schema from json file
        $filePath = resource_path('swiftype/schema.json');
        $schemaJsonContents = file_get_contents($filePath);
        $schema = json_decode($schemaJsonContents, true);
        app(Engine::class)::updateSchema($schema);
    }
}
