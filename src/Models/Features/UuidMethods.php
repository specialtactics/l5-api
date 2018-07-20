<?php

namespace Specialtactics\L5Api\Models\Features;

use Webpatser\Uuid\Uuid as UuidValidator;

/**
 * @deprecated This trait is deprecated - it was designed to allow parallel UUID and ID keys, no longer used
 *
 * Trait UuidMethods
 *
 * @package Specialtactics\L5Api\Models\Features
 */
trait UuidMethods
{
    /************************************************************
     * Adding UUID related functionality
     ***********************************************************/

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
     * Get the table qualified key name.
     *
     * @return string
     */
    public function getQualifiedUuidKeyName()
    {
        return $this->qualifyColumn($this->getUuidKeyName());
    }

    /************************************************************
     * Wrappers for eloquent functions
     *
     * These will check if the ID is a UUID, and redirect
     * the function as appropriate
     *
     * Note: PCRE compiles regexp to bytecode using PHP's JIT,
     * so it is very fast
     ***********************************************************/

    /**
     * Wrapper to allow both IDs and UUIDs to be used
     *
     * @param  array|int  $ids
     * @return int
     */
    public static function destroy($ids)
    {
        if (UuidValidator::validate($ids)) {
            return static::destroyByUuid($ids);
        } else {
            return parent::destroy($ids);
        }
    }

    /************************************************************
     * Modified Eloquent functions for UUIDs
     ***********************************************************/

    /**
     * Destroy the models for the given UUIDs.
     *
     * @param  array|string  $uuids
     * @return int
     */
    public static function destroyByUuid($uuids)
    {
        // We'll initialize a count here so we will return the total number of deletes
        // for the operation. The developers can then check this number as a boolean
        // type value or get this total count of records deleted for logging, etc.
        $count = 0;

        $ids = is_array($uuids) ? $uuids : func_get_args();

        // We will actually pull the models from the database table and call delete on
        // each of them individually so that their events get fired properly with a
        // correct set of attributes in case the developers wants to check these.
        $key = ($instance = new static)->getUuidKeyName();

        foreach ($instance->whereIn($key, $ids)->get() as $model) {
            if ($model->delete()) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Delete the model from the database.
     *
     * @return bool|null
     *
     * @throws \Exception
     */
    public function deleteByUuid()
    {
        if (is_null($this->getUuidKeyName())) {
            throw new Exception('No primary key defined on model.');
        }

        // If the model doesn't exist, there is nothing to delete so we'll just return
        // immediately and not do anything else. Otherwise, we will continue with a
        // deletion process on the model, firing the proper events, and so forth.
        if (! $this->exists) {
            return;
        }

        if ($this->fireModelEvent('deleting') === false) {
            return false;
        }

        // Here, we'll touch the owning models, verifying these timestamps get updated
        // for the models. This will allow any caching to get broken on the parents
        // by the timestamp. Then we will go ahead and delete the model instance.
        $this->touchOwners();

        $this->performDeleteOnModelByUuid();

        // Once the model has been deleted, we will fire off the deleted event so that
        // the developers may hook into post-delete operations. We will then return
        // a boolean true as the delete is presumably successful on the database.
        $this->fireModelEvent('deleted', false);

        return true;
    }

    /**
     * Perform the actual delete query on this model instance.
     *
     * @return void
     */
    protected function performDeleteOnModelByUuid()
    {
        $this->setUuidKeysForSaveQuery($this->newQueryWithoutScopes())->delete();

        $this->exists = false;
    }

    /**
     * Set the UUID keys for a save update query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function setUuidKeysForSaveQuery(Builder $query)
    {
        $query->where($this->getUuidKeyName(), '=', $this->getUuidKeyForSaveQuery());

        return $query;
    }

    /**
     * Get the UUID key value for a save query.
     *
     * @return mixed
     */
    protected function getUuidKeyForSaveQuery()
    {
        return $this->original[$this->getUuidKeyName()]
            ?? $this->getUuidKey();
    }
}