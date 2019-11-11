<?php

namespace Loonpwn\Swiftype\Console\Commands;

use Illuminate\Console\Command;

class SyncDocuments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'swiftype:sync-documents';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command syncs the current database with swiftypes';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        dispatch(new \Loonpwn\Swiftype\Jobs\SyncDocuments);
    }
}
