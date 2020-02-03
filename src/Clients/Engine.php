<?php

namespace Loonpwn\Swiftype\Clients;

use Elastic\AppSearch\Client\Client;
use Illuminate\Support\Collection;
use Loonpwn\Swiftype\Exceptions\MissingSwiftypeConfigException;

class Engine
{
    public const MAX_PAGE_SIZE = 100;

    /**
     * @var Client
     */
    protected $client;

    protected $engineName;

    public function __construct(Client $client)
    {
        $this->client = $client;

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
     * @param $options array An array of the search query, filters, sorts, etc to apply to the search.
     *
     * @return array An array of search results matching the issued query
     */
    public function search(string $query, $options = [])
    {
        return $this->client->search($this->engineName, $query, $options);
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
                return $this->client->indexDocuments($this->engineName, $documentsChunk->toArray());
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
     * @param array $documentIds An array of document ids
     *
     * @return array An array of true/false elements indicated success or failure of the creation or update of each individual document
     */
    public function deleteDocuments($documentIds)
    {
        // no document ids to delete
        if (empty($documentIds)) {
            return [];
        }

        return $this->client->deleteDocuments($this->engineName, $documentIds);
    }

    /**
     * Lists all documents. Note that this will only return a 100 results maximum.
     *
     * @param int $page
     * @param int $pageSize
     * @return mixed Documents
     */
    public function listDocuments($page = 1, $pageSize = self::MAX_PAGE_SIZE)
    {
        return $this->client->listDocuments($this->engineName, $page, min(self::MAX_PAGE_SIZE, $pageSize));
    }

    /**
     * Loops though each swiftype page and calls the action with the results.
     *
     * @param callable $action
     * @param int $pageSize How many documents to show per page chunk
     * @param int $page Which page to start from
     */
    public function listAllDocumentsByPages(callable $action, $page = 1, $pageSize = self::MAX_PAGE_SIZE)
    {
        // start with page 1
        $currentPage = $page;
        $finalPage = 1;
        while ($currentPage <= $finalPage) {

            // Swiftype paginates results 100 per page
            $chunkResult = $this->listDocuments($currentPage, $pageSize);

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
     * Sends a request to swiftype to update the schema.
     *
     * @param array $schema
     * @return mixed
     */
    public function updateSchema(array $schema)
    {
        return $this->client->updateSchema($this->engineName, $schema);
    }
}
