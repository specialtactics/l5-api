<?php
namespace Specialtactics\L5Api\Models;

use App\Transformers\BaseTransformer;
use Illuminate\Database\Eloquent\Model;
use Uuid;
use Specialtactics\L5Api\Transformers\RestfulTransformer;

class RestfulModel extends Model {
    /**
     * Every model should generally have an incrementing primary integer key.
     * An exception may be pivot tables
     *
     * @var int Auto increments integer key
     */
    public $primaryKey = 'user_id';

    /**
     * Every model should have a UUID key, which will be returned to API consumers.
     * The only exception to this may be entities with very vast amounts of records, which never require referencing
     * for the purposes of updating or deleting by API consumers
     *
     * @var string UUID key
     */
    public $uuidKey = 'user_uuid';

    /**
     * These attributes (in addition to primary & uuid keys) are not allowed to be updated explicitly through
     *  API routes of update and put. They can still be updated internally by Laravel, and your own code.
     *
     * @var array Attributes to disallow updating through an API update or put
     */
    public $immutableAttributes = ['created_at', 'deleted_at'];

    /**
     * Acts like $with (eager loads relations), however only for immediate controller requests for that object
     * This is useful if you want to use "with" for immediate resource routes, however don't want these relations
     *  always loaded in various service functions, for performance reasons
     *
     * @var array Relations to load implicitly by Restful controllers
     */
    public static $localWith = [];

    /**
     * You can define a custom transformer for a model, if you wish to override the functionality of the Base transformer
     *
     * @var null|RestfulTransformer The transformer to use for this model
     */
    public static $transformer = null;

    /**
     * Boot the model
     */
    public static function boot()
    {
        parent::boot();

        // If the PK(s) are missing, generate them
        static::creating(function(RestfulModel $model) {
            $uuidKeyName = $model->getUuidKeyName();

            if (!array_key_exists($uuidKeyName, $model->getAttributes())) {
                $model->$uuidKeyName = Uuid::generate(4);
            }
        });
    }

    /**
     * Get the UUID key for the model.
     *
     * @return string
     */
    public function getUuidKeyName()
    {
        return $this->uuidKey;
    }

    /**
     * Get the value of the model's UUID key.
     *
     * @return mixed
     */
    public function getUuidKey()
    {
        return $this->getAttribute($this->getUuidKeyName());
    }

    /**
     * Return this model's transformer, or a generic one if a specific one is not defined for the model
     *
     * @return BaseTransformer
     */
    public static function getTransformer() {
        return is_null(static::$transformer) ? new BaseTransformer : new static::$transformer;
    }

}
