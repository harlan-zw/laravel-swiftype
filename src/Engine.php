<?php

namespace Loonpwn\Swiftype;

class Engine
{
	private $client;

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
	 * @param string $query The search query
	 *
	 * @param $searchFields array The fields to search on and their weights
	 *
	 * @return array An array of search results matching the issued query
	 */
	public function search($query, $searchFields)
	{
		$response = $this->client->get('search',
			[
				'query' => [
					'query' => $query,
					'search_fields' => $searchFields
				]
			]);

		return json_decode($response->getBody()->getContents(), true);
	}

	/**
	 * Create or update a set of documents in an engine within a specific document_type.
	 *
	 * @param Eloquent $document
	 *
	 * @return array An array of true/false elements indicated success or failure of the creation or update of each individual document
	 */
	public function createOrUpdateDocument($document)
	{
		$response = $this->client->post('documents', ['json' => $document->getAttributesSwiftypeTransformed()]);

		return json_decode($response->getBody()->getContents(), true);
	}

	/**
	 * Delete a document from the engine using the document id.
	 *
	 * @param mixed $document_id The document to delete
	 *
	 * @return array An array of true/false elements indicated success or failure of the creation or update of each individual document
	 */
	public function deleteDocument($document_id)
	{
		$this->deleteDocuments([$document_id]);
	}

	/**
	 * Delete documents based on the document ids.
	 *
	 * @param array $document_ids An array of document ids
	 *
	 * @return array An array of true/false elements indicated success or failure of the creation or update of each individual document
	 */
	public function deleteDocuments($document_ids)
	{
		$response = $this->client->delete('documents', ['json' => $document_ids]);

		return json_decode($response->getBody()->getContents(), true);
	}

	public function listDocuments()
	{
		$response = $this->client->get('documents/list');

		return json_decode($response->getBody()->getContents(), true);
	}

	/**
	 * Goes through all documents and batches them up to be deleted.
	 */
	public function purgeAllDocuments()
	{
		$ids = [];
		foreach ($this->listDocuments()['results'] as $result) {
			$ids[] = $result['id'];
		}
		// check if there are any documents to delete, otherwise return true
		if (empty($ids)) {
			return true;
		}

		return $this->deleteDocuments($ids);
	}
}
