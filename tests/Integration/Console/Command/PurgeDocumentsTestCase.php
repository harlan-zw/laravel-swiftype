<?php

namespace Loonpwn\Swiftype\Tests\Integration\Console\Command;

use Loonpwn\Swiftype\Console\Commands\PurgeDocuments;
use Loonpwn\Swiftype\Tests\BaseTestCase;

class PurgeDocumentsTestCase extends BaseTestCase
{
    /**
     * @test
     */
    public function can_call_command()
    {
        $this->indexSeedDocuments();

        $this->artisan(PurgeDocuments::class);

        $this->assertEmpty($this->engine->listDocuments()['results']);
    }
}
