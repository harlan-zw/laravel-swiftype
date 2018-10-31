<?php
namespace Loonpwn\Swiftype\Traits;


use Loonpwn\Swiftype\Facades\Swiftype;
use Loonpwn\Swiftype\Facades\SwiftypeEngine;

trait ExistsAsSwiftypeDocument {

    public static $swiftypeEngine;

    public static function bootExistsAsSwiftypeDocument() {
        static::updating(function($model) {
           SwiftypeEngine::createOrUpdateDocument($model);
        });
        static::creating(function($model) {
            Swiftype::engine(self::$swiftypeEngine)->createOrUpdateDocument($model);
        });
        static::deleting(function($model) {
            Swiftype::engine(self::$swiftypeEngine)->deleteDocument($model);
        });
    }

	public function getAttributesSwiftypeTransformed() {
		$attributes = $this->getAttributes();
		// make sure that there is an id field set for swiftype
		if ($this->getKeyName() !== 'id') {
			unset($attributes[$this->getKeyName()]);
			$attributes['id'] = $this->getKey();
		}
		return $attributes;
	}

}
