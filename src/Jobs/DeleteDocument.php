<?php

namespace Loonpwn\Swiftype\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Loonpwn\Swiftype\Clients\Engine;

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
        app(Engine::class)->deleteDocument($this->documentId);
    }
}
