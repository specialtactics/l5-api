<?php

namespace Specialtactics\L5Api\Models;

use Exception;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Illuminate\Database\Eloquent\Model;
use Specialtactics\L5Api\Transformers\RestfulTransformer;
use App\Transformers\BaseTransformer;
use App\Models\User;

class RestfulModel extends Model
{

    use Features\UuidMethods;

    /**
     * Every model should generally have an incrementing primary integer key.
     * An exception may be pivot tables
     *
     * @var int Auto increments integer key
     */
    public $primaryKey = '';

    /**
     * Every model should have a UUID key, which will be returned to API consumers.
     * The only exception to this may be entities with very vast amounts of records, which never require referencing
     * for the purposes of updating or deleting by API consumers. In that case, make this null.
     *
     * @var string UUID key
     */
    public $uuidKey = '';

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
     * @var null|RestfulTransformer The transformer to use for this model, if overriding the default
     */
    public static $transformer = null;

    /**
     * Return the validation rules for this model
     *
     * @return array Validation rules to be used for the model when creating it
     */
    public function getValidationRules()
    {
        return [];
    }

    /**
     * Return the validation rules for this model's update operations
     * In most cases, they will be the same as for the create operations
     *
     * @return array Validation roles to use for updating model
     */
    public function getValidationRulesUpdating()
    {
        return $this->getValidationRules();
    }

    /**
     * Return any custom validation rule messages to be used
     *
     * @return array
     */
    public function getValidationMessages()
    {
        return [];
    }

    /**
     * Boot the model
     *
     * Add various functionality in the model lifecycle hooks
     */
    public static function boot()
    {
        parent::boot();

        // Add functionality for creating a model
        static::creating(function (RestfulModel $model) {
            // If the PK(s) are missing, generate them
            $uuidKeyName = $model->getUuidKeyName();

            if (!array_key_exists($uuidKeyName, $model->getAttributes())) {
                $model->$uuidKeyName = Uuid::uuid4()->toString();
            }
        });

        // Add functionality for updating a model
        static::updating(function (RestfulModel $model) {
            // Disallow updating UUID keys
            if ($model->getAttribute($model->getUuidKeyName()) != $model->getOriginal($model->getUuidKeyName())) {
                throw new BadRequestHttpException('Updating the UUID of a resource is not allowed.');
            }

            // Disallow updating immutable attributes
            if (! empty($model->immutableAttributes)) {
                // For each immutable attribute, check if they have changed
                foreach ($model->immutableAttributes as $attributeName) {
                    if ($model->getOriginal($attributeName) != $model->getAttribute($attributeName)) {
                        throw new BadRequestHttpException('Updating the "'.camel_case($attributeName).'" attribute is not allowed.');
                    }
                }
            }
        });
    }

    /**
     * Return this model's transformer, or a generic one if a specific one is not defined for the model
     *
     * @return BaseTransformer
     */
    public static function getTransformer()
    {
        return is_null(static::$transformer) ? new BaseTransformer : new static::$transformer;
    }

    /**
     * When Laravel creates a new model, it will add any new attributes (such as UUID) at the end. When a create
     * operation such as a POST returns the new resource, the UUID will thus be at the end, which doesn't look nice.
     * For purely aesthetic reasons, we have this function to conduct a simple reorder operation to move the UUID
     * attribute to the head of the attributes array
     *
     * This will be used at the end of create-related controller functions
     *
     * @return void
     */
    public function orderAttributesUuidFirst()
    {
        if ($this->getUuidKeyName()) {
            $UuidValue = $this->getUuidKey();
            unset($this->attributes[$this->getUuidKeyName()]);
            $this->attributes = [$this->getUuidKeyName() => $UuidValue] + $this->attributes;
        }
    }

    /**
     * This function can be used to add conditions to the query builder,
     * which will specify the user's ownership of the model
     *
     *
     * @param App\Models\User $user
     * @param Illuminate\Database\Eloquent\Builder $query
     * @return Illuminate\Database\Eloquent\Builder|null
     */
    public function addQueryBuilderForOwner(User $user, $query)
    {
        return $query;
    }

    /************************************************************
     * Extending Laravel Functions Below
     ***********************************************************/

    /**
     * We're extending the existing Laravel Builder
     *
     * Create a new Eloquent query builder for the model.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function newEloquentBuilder($query)
    {
        return new Builder($query);
    }
}
