<?php

namespace Loonpwn\Swiftype\Clients;

use Elastic\EnterpriseSearch\AppSearch\Endpoints as AppEndpoints;
use Elastic\EnterpriseSearch\AppSearch\Request\DeleteDocuments;
use Elastic\EnterpriseSearch\AppSearch\Request\IndexDocuments;
use Elastic\EnterpriseSearch\AppSearch\Request\ListDocuments;
use Elastic\EnterpriseSearch\AppSearch\Request\PutSchema;
use Elastic\EnterpriseSearch\AppSearch\Request\Search;
use Elastic\EnterpriseSearch\AppSearch\Schema\SchemaData;
use Elastic\EnterpriseSearch\AppSearch\Schema\SearchRequestParams;
use Elastic\EnterpriseSearch\Response\Response;
use Illuminate\Support\Collection;
use Loonpwn\Swiftype\Exceptions\MissingSwiftypeConfigException;

class Engine
{
    public const MAX_PAGE_SIZE = 100;

    protected AppEndpoints $client;
    protected string $engineName;

    public function __construct(AppEndpoints $client, $engineName = '')
    {
        $this->client = $client;

        $this->engineName = $engineName;
        // Allow the engineName to be set from a parent class
        if (empty($this->engineName)) {
            if (empty(config('swiftype.default_engine'))) {
                throw new MissingSwiftypeConfigException('SWIFTYPE_DEFAULT_ENGINE');
            }

            $this->engineName = config('swiftype.default_engine');
        }
    }

    /**
     * Issue a query to an engine within a specific document_type.
     *
     * @see https://swiftype.com/documentation/app-search/api/search
     *
     * @param string $query The search query
     *
     * @param $options SearchRequestParams|null An array of the search query, filters, sorts, etc to apply to the search.
     *
     * @return array An array of search results matching the issued query
     */
    public function search(string $query, SearchRequestParams $options = null)
    {
        if ($options === null) {
            $options = new SearchRequestParams;
        }
        $options->query = $query;

        return $this->client->search(new Search($this->engineName, $options));
    }

    /**
     * Create or update a set of documents in an engine within a specific document_type.
     *
     * @see https://swiftype.com/documentation/app-search/api/documents
     *
     * @param $document
     * @return array An array of true/false elements indicated success or failure of the creation or update of each individual document
     */
    public function indexDocument($document)
    {
        return $this->indexDocuments([$document]);
    }

    /**
     * Create or update a set of documents in an engine within a specific document_type.
     *
     * @see https://swiftype.com/documentation/app-search/api/documents
     *
     * @param array $documents
     * @return array An array of true/false elements indicated success or failure of the creation or update of each individual document
     */
    public function indexDocuments(array $documents)
    {
        return collect($documents)
            ->chunk(self::MAX_PAGE_SIZE)
            ->map(function (Collection $documentsChunk) {
                return $this->client
                    ->indexDocuments(new IndexDocuments($this->engineName, $documentsChunk->values()->toArray()))
                    ->asArray();
            })
            ->flatten()
            ->toArray();
    }

    /**
     * Delete a document from the engine using the document id.
     *
     * @param mixed $documentId The document to delete
     *
     * @return array An array of true/false elements indicated success or failure of the creation or update of each individual document
     */
    public function deleteDocument($documentId)
    {
        return $this->deleteDocuments([$documentId]);
    }

    /**
     * Delete documents based on the document ids.
     *
     * @see https://swiftype.com/documentation/app-search/api/documents
     *
     * @return array An array of true/false elements indicated success or failure of the creation or update of each individual document
     */
    public function deleteDocuments(array $documentIds)
    {
        // no document ids to delete
        if (empty($documentIds)) {
            return [];
        }

        $response = $this->client->deleteDocuments(new DeleteDocuments($this->engineName, $documentIds));

        return $response->asArray();
    }

    /**
     * Lists all documents. Note that this will only return a 100 results maximum.
     */
    public function listDocuments(int $page = 1, int $pageSize = self::MAX_PAGE_SIZE): Response
    {
        $request = new ListDocuments($this->engineName);
        $request->setPageSize(min(self::MAX_PAGE_SIZE, $pageSize));
        $request->setCurrentPage($page);

        return $this->client->listDocuments($request);
    }

    /**
     * Loops though each swiftype page and calls the action with the results.
     */
    public function listAllDocumentsByPages(callable $action, int $page = 1, int $pageSize = self::MAX_PAGE_SIZE): void
    {
        // start with page 1
        $currentPage = $page;
        $finalPage = 1;
        while ($currentPage <= $finalPage) {

            // Swiftype paginates results 100 per page
            $chunkResult = $this->listDocuments($currentPage, $pageSize)->asArray();

            // pagination data
            $finalPage = $chunkResult['meta']['page']['total_pages'];
            $currentPage = $chunkResult['meta']['page']['current'];

            // If results are empty swiftype killed itself
            if (empty($chunkResult['results'])) {
                break;
            }

            $action($chunkResult['results'], $currentPage, $finalPage);

            $currentPage++;
        }
    }

    /**
     * Goes through all documents and batches them up to be deleted.
     *
     * This internally isn't very performant since we can't tell Swiftype to just delete all documents. Instead we need
     * to iterate through all documents, collect their IDs and then request those IDs to be deleted
     *
     * @return array Array of IDs that were deleted
     */
    public function purgeAllDocuments()
    {
        $allIds = collect();
        $this->listAllDocumentsByPages(function ($chunk) use (&$allIds) {
            $ids = collect($chunk)->map->id->toArray();
            $this->deleteDocuments($ids);
            $allIds->push($ids);
        });

        return $allIds->flatten()->toArray();
    }

    /**
     * Sends a request to Swifttype to update the schema.
     */
    public function updateSchema(array $schema): Response
    {
        $schemaData = new SchemaData;
        foreach ($schema as $key => $value) {
            $schemaData->$key = $value;
        }

        return $this->client->putSchema(new PutSchema($this->engineName, $schemaData));
    }
}
