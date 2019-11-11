<?php

namespace Loonpwn\Swiftype\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Loonpwn\Swiftype\Facades\SwiftypeEngine;

class DeleteDocument implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $documentId;

    /**
     * Create a new job instance.
     *
     * @param $documentId
     */
    public function __construct($documentId)
    {
        //
        $this->documentId = $documentId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        app(SwiftypeEngine::class)->deleteDocument($this->documentId);
    }
}
