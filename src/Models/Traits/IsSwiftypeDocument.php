<?php

namespace Loonpwn\Swiftype\Models\Traits;

use Loonpwn\Swiftype\Jobs\DeleteDocument;
use Loonpwn\Swiftype\Jobs\IndexDocument;
use Loonpwn\Swiftype\Models\Transformers\DocumentTransformer;

trait IsSwiftypeDocument
{
    public static function bootIsSwiftypeDocument()
    {
        // when the model is saved we may need to update swiftype
        static::saved(function ($model) {
            $model->maybeIndexWithSwiftype();
        });
        static::deleted(function ($model) {
            dispatch(new DeleteDocument($model->getKey()));
        });
    }

    public function maybeIndexWithSwiftype()
    {
        // are we syncing
        if (! $this->shouldSyncSwiftypeOnSave()) {
            return;
        }
        $data = $this->getSwiftypeAttributes();
        // is there any data to sync
        if (! empty($data)) {
            dispatch(new IndexDocument($data));
        }
    }

    /**
     * Should model changes be pushed to Swiftype. Excludes deleting.
     * @return bool
     */
    public function shouldSyncSwiftypeOnSave()
    {
        return true;
    }

    /**
     * Get the mapped attribute values for Swiftype.
     * @return mixed|null
     */
    public function getSwiftypeAttributes()
    {
        return transform($this, new DocumentTransformer());
    }
}
