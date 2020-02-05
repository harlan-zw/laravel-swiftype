<?php

namespace Loonpwn\Swiftype;

use Laravel\Scout\Builder;
use Loonpwn\Swiftype\Clients\Engine;

class SwiftypeEngine extends \Laravel\Scout\Engines\Engine
{
    /**
     * The Swiftype client.
     *
     * @var Engine
     */
    protected $swiftype;

    /**
     * Create a new engine instance.
     *
     * @param Engine $swiftype
     * @return void
     */
    public function __construct(Engine $swiftype)
    {
        $this->swiftype = $swiftype;
    }

    /**
     * Update the given model in the index.
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $models
     * @return void
     */
    public function update($models)
    {
        if ($models->isEmpty()) {
            return;
        }

        $objects = $models->map(function ($model) {
            if (empty($searchableData = $model->toSearchableArray())) {
                return;
            }

            return array_merge(
                ['id' => $model->getScoutKey()],
                $searchableData,
                $model->scoutMetadata()
            );
        })->filter()->values()->all();

        if (! empty($objects)) {
            $this->swiftype->indexDocuments($objects);
        }
    }

    /**
     * Remove the given model from the index.
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $models
     * @return void
     */
    public function delete($models)
    {
        $this->swiftype->deleteDocuments(
            $models->map(function ($model) {
                return $model->getKey();
            })->toArray()
        );
    }

    /**
     * Perform the given search on the engine.
     *
     * @param  \Laravel\Scout\Builder  $builder
     * @return mixed
     */
    public function search(Builder $builder)
    {
        return $this->performSearch($builder, array_filter([
            'filters' => $this->filters($builder),
            'search_fields' => $builder->model->getScoutSearchFields(),
            'page' => [
                'size' => $builder->limit ?? 10,
            ],
        ]));
    }

    /**
     * Perform the given search on the engine.
     *
     * @param  \Laravel\Scout\Builder  $builder
     * @param  int  $perPage
     * @param  int  $page
     * @return mixed
     */
    public function paginate(Builder $builder, $perPage, $page)
    {
        return $this->performSearch($builder, [
            'filters' => $this->filters($builder),
            'page' => [
                'current' => $page - 1,
                'size' => $perPage ?? 10,
            ],
        ]);
    }

    /**
     * Perform the given search on the engine.
     *
     * @param  \Laravel\Scout\Builder  $builder
     * @param  array  $options
     * @return mixed
     */
    protected function performSearch(Builder $builder, array $options = [])
    {
        if ($builder->callback) {
            $options = call_user_func(
                $builder->callback,
                $options
            );
        }

        return $this->swiftype->search($builder->query, $options);
    }

    /**
     * Get the filter array for the query.
     *
     * @param  \Laravel\Scout\Builder  $builder
     * @return array
     */
    protected function filters(Builder $builder)
    {
        return collect($builder->wheres)->map(function ($value, $key) {
            return [ $value ];
        })->all();
    }

    /**
     * Pluck and return the primary keys of the given results.
     *
     * @param  mixed  $results
     * @return \Illuminate\Support\Collection
     */
    public function mapIds($results)
    {
        return collect($results['results'])->pluck('id.raw')->values();
    }

    /**
     * Map the given results to instances of the given model.
     *
     * @param  \Laravel\Scout\Builder  $builder
     * @param  mixed  $results
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function map(Builder $builder, $results, $model)
    {
        if ($results['meta']['page']['total_results'] === 0) {
            return $model->newCollection();
        }

        $objectIds = collect($results['results'])->pluck('id.raw')->values()->all();
        $objectIdPositions = array_flip($objectIds);

        return $model->getScoutModelsByIds(
            $builder, $objectIds
        )->filter(function ($model) use ($objectIds) {
            return in_array($model->getScoutKey(), $objectIds);
        })->sortBy(function ($model) use ($objectIdPositions) {
            return $objectIdPositions[$model->getScoutKey()];
        })->values();
    }

    /**
     * Get the total count from a raw result returned by the engine.
     *
     * @param  mixed  $results
     * @return int
     */
    public function getTotalCount($results)
    {
        return $results['meta']['page']['total_results'];
    }

    /**
     * Flush all of the model's records from the engine.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function flush($model = null)
    {
        $this->swiftype->purgeAllDocuments();
    }

    /**
     * Dynamically call the Swiftype client instance.
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->swiftype->$method(...$parameters);
    }
}
