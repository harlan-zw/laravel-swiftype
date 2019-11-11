<?php

namespace Loonpwn\Swiftype\Tests\Integration\Console\Command;

use Illuminate\Support\Facades\Event;
use Loonpwn\Swiftype\Console\Commands\SyncDocuments;
use Loonpwn\Swiftype\Tests\App\Models\User;
use Loonpwn\Swiftype\Tests\BaseTestCase;

class SyncDocumentsTest extends BaseTestCase
{

    /**
     * @test
     */
    public function can_call_command() {
        // create 5 documents
        $documents = $this->indexSeedDocuments(5);
        // delete 2 documents - count 3
        $documents->take(2)->each(function($document) {
          $document->delete();
        });

        Event::fake();

        // delete 2 more documents (without events) - count 1
        User::get()->take(2)->each(function($document) {
            $document->delete();
        });

        // add 5 new documents (without events)
        $this->indexSeedDocuments(5);

        // sync should try and 2 delete documents and add 5, totalling 6 results
        $this->artisan(SyncDocuments::class);

        $this->assertEquals(6, count($this->engine->listDocuments()['results']));
    }

}