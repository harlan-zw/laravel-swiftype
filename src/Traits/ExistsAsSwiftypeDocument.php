<?php

namespace Loonpwn\Swiftype\Traits;

use Loonpwn\Swiftype\Facades\Swiftype;
use Loonpwn\Swiftype\Facades\SwiftypeEngine;

trait ExistsAsSwiftypeDocument
{
    public static function bootExistsAsSwiftypeDocument()
    {
        static::updating(function ($model) {
            $data = $model->getModelSwiftypeTransformed();
            if (!empty($data)) {
                SwiftypeEngine::createOrUpdateDocument($data);
            }
        });
        static::created(function ($model) {
            $data = $model->getModelSwiftypeTransformed();
            if (!empty($data)) {
                SwiftypeEngine::createOrUpdateDocument($data);
            }
        });
        static::deleting(function ($model) {
            SwiftypeEngine::deleteDocument($model->getKey());
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
