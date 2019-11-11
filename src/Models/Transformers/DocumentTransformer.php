<?php


namespace Loonpwn\Swiftype\Models\Transformers;


use Illuminate\Database\Eloquent\Model;

class DocumentTransformer
{
    public function __invoke(Model $model) {
        return array_merge(
            // Swiftype needs the key name to be on the id key
            [
                'id' => $model->getKey(),
            ],
            $model->getAttributes()
        );
    }
}