<?php

namespace Loonpwn\Swiftype\Console\Commands;

use Illuminate\Console\Command;
use Loonpwn\Swiftype\Facades\SwiftypeEngine;

class PurgeAllDocuments extends Command
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
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
    	SwiftypeEngine::purgeAllDocuments();
    }
}
