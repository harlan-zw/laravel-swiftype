<?php

namespace Loonpwn\Swiftype\Tests\Feature;

use Loonpwn\Swiftype\Facades\SwiftypeEngine;
use Loonpwn\Swiftype\Tests\BaseSwiftypeTest;
use Loonpwn\Swiftype\Tests\Models\TestModel;

class SwiftypeEngineFacadeTest extends BaseSwiftypeTest
{
    public function testSeedDocuments()
    {
        $this->log('Starting testSeedDocuments');
        $modelData = [];
        for ($i = 0; $i < 5; $i++) {
            $modelData[] = (new TestModel())->getModelSwiftypeTransformed();
        }
        $results = SwiftypeEngine::createOrUpdateDocuments($modelData);
        $this->assertCount(5, $results, 'All ids are returned');
        $this->log('Created documents');
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testDocumentListWorks()
    {
        $this->log('Starting testDocumentListWorks');
        $documents = SwiftypeEngine::listDocuments();
        $this->assertArrayHasKey('results', $documents, 'We can list engine documents');
        $this->log('Found documents', count($documents['results']));
    }

    public function testListAllDocumentsWorks()
    {
        $this->log('Starting testListAllDocumentsWorks');
        $count = 0;
        SwiftypeEngine::listAllDocumentsByPages(function ($results, $page, $total) use (&$count) {
            $this->assertNotEmpty($results, 'Page has results');
            $count++;
            $this->log('listed results for page', count($results), $page.'/'.$total);
        });
        $this->assertTrue($count > 0, 'Had pages');
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testSearchWorks()
    {
        $results = SwiftypeEngine::listDocuments();

        $query = $results['results'][0]['first_name'];

        $this->log('Starting testSearchWorks');
        $documents = SwiftypeEngine::searchWithQuery($query);
        $this->assertArrayHasKey('results', $documents, 'We can search engine documents');
        $this->log('Search for '.$query.': ', count($documents['results']));
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testPurgeDocuments()
    {
        $this->log('Starting testPurgeDocuments');
        $documentIds = SwiftypeEngine::purgeAllDocuments();

        $this->assertNotEmpty($documentIds, 'Purge documents returned a valid response');
        $this->log('Deleted documents', count($documentIds));
    }
}
