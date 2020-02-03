<?php

namespace Loonpwn\Swiftype\Console\Commands;

use Illuminate\Console\Command;
use Loonpwn\Swiftype\Clients\Engine;

class PurgeDocuments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'swiftype:purge-documents';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Removes all documents from Swiftype.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        app(Engine::class)->purgeAllDocuments();
    }
}
