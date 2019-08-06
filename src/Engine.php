<?php

namespace Loonpwn\Swiftype;

class Engine
{
    public const MAX_PAGE_SIZE = 100;

    protected $client;

    public function __construct()
    {
        $engine = config('swiftype.defaultEngine');
        $config = array_merge(config('swiftype'), [
            'apiUrl' => 'https://'.config('swiftype.hostIdentifier').'.api.swiftype.com/api/as/v1/engines/'.$engine.'/',
        ]);
        $this->client = new SwiftypeClient($config);
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
    public function searchWithQuery($query, $options = [])
    {
        $response = $this->client->get('search',
            [
                'json' => array_merge(['query' => $query], $options),
            ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Issue a query to an engine within a specific document_type.
     *
     * @see https://swiftype.com/documentation/app-search/api/search
     *
     * @param $options array An array of the query options, such as filters, sorts, etc to apply to the request
     *
     * @return array An array of search results matching the issued query
     */
    public function search($options = [])
    {
        $response = $this->client->get('search',
            [
                'json' => $options,
            ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Create or update a set of documents in an engine within a specific document_type.
     *
     * @see https://swiftype.com/documentation/app-search/api/documents
     *
     * @param Eloquent $document
     *
     * @return array An array of true/false elements indicated success or failure of the creation or update of each individual document
     */
    public function createOrUpdateDocument($data)
    {
        $response = $this->client->post('documents', ['json' => $data]);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Create or update a set of documents in an engine within a specific document_type.
     *
     * @see https://swiftype.com/documentation/app-search/api/documents
     *
     * @return array An array of true/false elements indicated success or failure of the creation or update of each individual document
     */
    public function createOrUpdateDocuments($data)
    {
        return collect($data)
            ->chunk(self::MAX_PAGE_SIZE)
            ->map(function($chunk) {
                $response = $this->client->post('documents', ['json' => $chunk]);
                return json_decode($response->getBody()->getContents(), true);
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
        $this->deleteDocuments([$documentId]);
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
        $response = $this->client->delete('documents', ['json' => $documentIds]);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Lists all documents. Note that this will only return a 100 results maximum.
     *
     * @return mixed Documents
     */
    public function listDocuments($page = 1, $pageSize = self::MAX_PAGE_SIZE)
    {
        $response = $this->client->get('documents/list', [
            'page' => [
                'current' => $page,
                // enforce 100 max
                'size' => min(self::MAX_PAGE_SIZE, $pageSize)
            ]
        ]);

        return json_decode($response->getBody()->getContents(), true);
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
        do {
            // Swiftype paginates results 100 per page
            $chunkResult = $this->listDocuments([
                'page' => [
                    'current' => $currentPage,
                    // enforce self::MAX_PAGE_SIZE max
                    'size' => min(self::MAX_PAGE_SIZE, $pageSize)
                ]
            ]);
            // pagination data
            $finalPage = $chunkResult['meta']['page']['total_pages'];
            $currentPage = $chunkResult['meta']['page']['current'];

            // If results are 0 swiftype killed itself
            if (empty($chunkResult['results'])) {
                break;
            }

            $action($chunkResult['results'], $currentPage, $finalPage);

            $currentPage++;
        } while ($currentPage <= $finalPage);
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
        $this->listAllDocumentsByPages(function($chunk) use (&$allIds) {
            $ids = collect($chunk)->map->id->toArray();
            $this->deleteDocuments($ids);
            $allIds->push($ids);
        });
        return $allIds->flatten()->toArray();
    }

}
