<?php

namespace Loonpwn\Swiftype\Traits;

use Loonpwn\Swiftype\Jobs\SyncDocument;
use Loonpwn\Swiftype\Jobs\DeleteDocument;

trait IsSwiftypeDocument
{
    public static function bootIsSwiftypeDocument()
    {
        static::updated(function ($model) {
            $data = $model->getSwiftypeAttributes();
            if (! empty($data)) {
                dispatch(new SyncDocument($data));
            }
        });
        static::created(function ($model) {
            $data = $model->getSwiftypeAttributes();
            if (! empty($data)) {
                dispatch(new SyncDocument($data));
            }
        });
        static::deleted(function ($model) {
            dispatch(new DeleteDocument($model->getKey()));
        });
    }

    public function getSwiftypeAttributes()
    {
        $attributes = $this->getAttributes();
        // make sure that there is an id field set for swiftype
        if ($this->getKeyName() !== 'id') {
            unset($attributes[$this->getKeyName()]);
            $attributes['id'] = $this->getKey();
        }

        return $attributes;
    }
}
