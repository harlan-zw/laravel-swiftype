<?php

namespace Loonpwn\Swiftype\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Loonpwn\Swiftype\Clients\Engine;

class SyncDocuments implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        /** @var Engine $engine */
        $engine = app(Engine::class);
        $models = config('swiftype.sync_models');

        if (empty($models)) {
            throw new \Exception('To use the SyncDocuments command add your models to sync in config/swiftype.php');
        }

        $documentIds = collect();
        $documentIdsToDelete = collect();
        $engine->listAllDocumentsByPages(function ($documents) use ($documentIds, $documentIdsToDelete, $models) {
            /** @var Collection $idsForCurrentPage */
            $idsForCurrentPage = collect($documents)->map->id;

            // find all models for the id
            $idsForCurrentPage = $idsForCurrentPage->filter(function ($id) use ($models, $documentIdsToDelete) {
                $foundModel = false;
                foreach ($models as $modelClass) {
                    /** @var Model $model */
                    $model = $modelClass::find($id);
                    if ($model) {
                        $foundModel = true;
                        // if the model is indexed but it shouldn't be
                        if (! $model->shouldBeSearchable()) {
                            $documentIdsToDelete->push($model->getKey());

                            return false;
                        }
                    }
                }
                if (! $foundModel) {
                    $documentIdsToDelete->push($id);

                    return false;
                }

                return true;
            });

            $documentIds->push($idsForCurrentPage->toArray());
        });

        $documentsToAdd = collect();
        foreach ($models as $modelClass) {
            /** @var Model $model */
            $models = $modelClass::whereNotIn((new $modelClass)->getKeyName(), $documentIds->flatten()->toArray())->get();
            foreach ($models as $model) {
                if ($model->shouldBeSearchable()) {
                    $documentsToAdd->push($model->toSearchableArray());
                }
            }
        }

        $engine->indexDocuments($documentsToAdd->toArray());

        if ($documentIdsToDelete->isNotEmpty()) {
            $engine->deleteDocuments($documentIdsToDelete->toArray());
        }
    }
}
