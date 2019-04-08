<?php

namespace Loonpwn\Swiftype\Traits;

use App\Jobs\Swiftype\SwiftypeDelete;
use App\Jobs\Swiftype\SwiftypeSync;
use Loonpwn\Swiftype\Facades\SwiftypeEngine;

trait ExistsAsSwiftypeDocument
{
    public static function bootExistsAsSwiftypeDocument()
    {
        static::updated(function ($model) {
            $data = $model->getModelSwiftypeTransformed();
            if (!empty($data)) {
                dispatch(new SwiftypeSync($data));
            }
        });
        static::created(function ($model) {
            $data = $model->getModelSwiftypeTransformed();
            if (!empty($data)) {
                dispatch(new SwiftypeSync($data));
            }
        });
        static::deleted(function ($model) {
            dispatch(new SwiftypeDelete($model->getKey()));
        });
    }

    public function getModelSwiftypeTransformed()
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
