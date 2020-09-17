<?php

namespace Loonpwn\Swiftype\Tests\Integration;

use Loonpwn\Swiftype\Tests\App\Models\User;
use Loonpwn\Swiftype\Tests\BaseTestCase;

class EngineFacadeTestCase extends BaseTestCase
{
    /**
     * @test
     */
    public function can_index_documents()
    {
        User::factory()->count(5)->create();

        $results = $this->engine->listDocuments();

        $this->assertSame(5, count($results['results']), 'Documents exist in the engine');
    }

    /**
     * @test
     */
    public function can_list_documents()
    {
        $this->log('Starting testDocumentListWorks');
        $documents = $this->engine->listDocuments();
        $this->assertArrayHasKey('results', $documents, 'We can list engine documents');
        $this->log('Found documents', count($documents['results']));
    }

    /**
     * @test
     */
    public function list_all_documents_by_pages_works()
    {
        $this->indexSeedDocuments(4);
        // need to give swiftype a chance for their cache to update
        sleep(1);
        $this->log('Starting testListAllDocumentsWorks');
        $count = 0;
        // should be 5 pages of results
        $this->engine->listAllDocumentsByPages(function ($results, $page, $total) use (&$count) {
            $this->assertNotEmpty($results, 'Page has results');
            $count++;
            $this->log('listed results for page', count($results), $page.'/'.$total);
        }, 1, 2);
        $this->assertEquals(2, $count, 'Had pages');
    }

    /**
     * @test
     */
    public function can_search_document()
    {
        $model = User::factory()->create();

        $this->log('Starting testSearchWorks');
        $results = $this->engine->search($model->name);

        $this->assertArrayHasKey('results', $results, 'We can search engine documents');
        $this->log('Search for '.$model->name.': ', count($results['results']));
    }
}
