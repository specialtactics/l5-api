<?php

namespace Specialtactics\L5Api\Models;

use Illuminate\Contracts\Support\Arrayable;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Webpatser\Uuid\Uuid;

/**
 * @deprecated This class is deprecated - it was designed to allow parallel UUID and ID keys, no longer used
 *
 * Class Builder
 *
 * This adds some functionality similar to existing provided by the Eloquent Builder, relating to UUIDs and
 * other API elements
 */
class UuidBuilder extends \Illuminate\Database\Eloquent\Builder
{
    /**
     * Find a model by its UUID key or throw a not found exception.
     *
     * @param  mixed  $uuid
     * @param  array  $columns
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Collection
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findOrFailByUuid($uuid, $columns = ['*'])
    {
        $result = $this->findByUuid($uuid, $columns);

        if (is_array($uuid)) {
            if (count($result) == count(array_unique($uuid))) {
                return $result;
            }
        } elseif (! is_null($result)) {
            return $result;
        }

        throw new NotFoundHttpException('Resource \'' . class_basename(get_class($this->model)) . '\' with given UUID ' . $uuid . ' not found');
    }

    /**
     * Find a model by its primary key.
     *
     * @param  mixed  $uuid
     * @param  array  $columns
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Collection|static[]|static|null
     */
    public function findByUuid($uuid, $columns = ['*'])
    {
        if (is_array($uuid) || $uuid instanceof Arrayable) {
            return $this->findManyByUuid($uuid, $columns);
        }

        return $this->whereUuidKey($uuid)->first($columns);
    }

    /**
     * Find multiple models by their UUID keys.
     *
     * @param  \Illuminate\Contracts\Support\Arrayable|array  $uuids
     * @param  array  $columns
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findManyByUuid($uuids, $columns = ['*'])
    {
        if (empty($uuids)) {
            return $this->model->newCollection();
        }

        return $this->whereUuidKey($uuids)->get($columns);
    }

    /**
     * Add a where clause on the UUID key to the query.
     *
     * @param  mixed  $uuid
     * @return $this
     */
    public function whereUuidKey($uuid)
    {
        if (is_array($uuid) || $uuid instanceof Arrayable) {
            $this->query->whereIn($this->model->getQualifiedUuidKeyName(), $uuid);

            return $this;
        }

        return $this->where($this->model->getQualifiedUuidKeyName(), '=', $uuid);
    }

    /************************************************************
     * Wrappers for builder functions
     *
     * These will check if the ID is a UUID, and redirect
     * the function as appropriate
     *
     * Note: PCRE compiles regexp to bytecode using PHP's JIT,
     * so it is very fast
     ***********************************************************/

    /**
     * Wrapper to allow both UUIDs and traditional IDs to be used
     *
     * @param mixed $id
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    public function findOrFail($id, $columns = ['*'])
    {
        if (Uuid::validate($id)) {
            return $this->findOrFailByUuid($id, $columns);
        } else {
            return parent::findOrFail($id, $columns);
        }
    }

    /**
     * Wrapper to allow both UUIDs and traditional IDs to be used
     *
     * @param mixed $id
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    public function find($id, $columns = ['*'])
    {
        if (Uuid::validate($id)) {
            return $this->findByUuid($id, $columns);
        } else {
            return parent::find($id, $columns);
        }
    }
}
