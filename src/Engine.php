<?php

namespace Loonpwn\Swiftype;

class Engine
{

    private $client;

    public function __construct($engine = null) {
        if (empty($engine)) {
            $engine = config('swiftype.defaultEngine');
        }

        $config = array_merge(config('swiftype'), [
            'apiUrl' => 'https://' . config('swiftype.hostIdentifier') . '.api.swiftype.com/api/as/v1/engines/' . $engine . '/'
        ]);
        $this->client = new SwiftypeClient($config);
    }

    /**
     * Issue a query to an engine within a specific document_type
     *
     * @param string $query The search query
     *
     * @return array An array of search results matching the issued query
     */
    public function search($query) {
        $response = $this->client->get('search', [ 'query' => [ 'query' => $query] ]);
        return json_decode( $response->getBody()->getContents(), true );
    }

    /**
     * Create or update a set of documents in an engine within a specific document_type
     *
     * @param Eloquent $document
     *
     * @return array An array of true/false elements indicated success or failure of the creation or update of each individual document
     */
    public function createOrUpdateDocument( $document ) {
        $response = $this->client->post( 'documents', [ 'json' => $document->getAttributesSwiftypeTransformed()] );
        return json_decode( $response->getBody()->getContents(), true );
    }

    /**
     * Delete a document from the engine using the eloquent model
     *
     * @param Eloquent $document The document to delete
     *
     * @return array An array of true/false elements indicated success or failure of the creation or update of each individual document
     */
    public function deleteDocument( $document ) {
        $response = $this->client->post( 'documents', [ 'json' => [ $document->getKey() ] ] );
        return json_decode( $response->getBody()->getContents(), true );
    }


    public function listDocuments() {
        $response = $this->client->get( 'documents/list');
        return json_decode( $response->getBody()->getContents(), true );
    }



}
