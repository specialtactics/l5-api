<?php

namespace Specialtactics\L5Api\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Specialtactics\L5Api\Helpers;

class RestfulChildController extends BaseRestfulController
{
    /**
     * Specify the parent model that you want to be associated with this controller. This is the parent model of the
     * primary model the controller deals with
     *
     * @var \App\Models\BaseModel $model
     */
    public static $parentModel = null;

    /**
     * These are the abilities which the authenticated user must be able to perform on the parent model
     * in order to perform the relevant action on the child model of this controller (array keys).
     *
     * Array keys are child resource operations, and values are matching abilities on parent model required
     *
     * Create means create a new
     *
     * @var array
     */
    public $parentAbilitiesRequired = [
        'create' => 'update',
        'view' => 'view',
        'viewAll' => 'view',
        'update' => 'own',
        'delete' => 'own',
    ];

    /**
     * Request to retrieve a collection of all items owned by the parent of this resource
     *
     * @param  string  $uuid
     * @param  Request  $request
     * @return \Dingo\Api\Http\Response
     */
    public function getAll($uuid, Request $request)
    {
        $this->authorizeUserAction('viewAll');

        $parentModel = static::$parentModel;
        $parentResource = $parentModel::findOrFail($uuid);

        // Authorize ability to view children models for parent
        $this->authorizeUserAction($this->parentAbilitiesRequired['view'], $parentResource);

        $model = static::$model;
        $resourceRelationName = Helpers::modelRelationName($model);

        // Form model's with relations for parent query
        $withArray = [];
        foreach ($model::getCollectionWith() as $modelRelation) {
            $withArray[] = $resourceRelationName . '.' . $modelRelation;
        }

        $withArray = array_merge([$resourceRelationName], $withArray);

        $parentResource = $parentResource->where($parentResource->getKeyName(), '=', $parentResource->getKey())->with($withArray)->first();

        $collection = $parentResource->getRelationValue($resourceRelationName);

        if ($collection == null) {
            $collection = new Collection();
        }

        return $this->response->collection($collection, $this->getTransformer());
    }

    /**
     * Request to retrieve a single child owned by the parent of this resource (hasOne relationship)
     *
     * @param  string  $uuid
     * @param  Request  $request
     * @return \Dingo\Api\Http\Response
     */
    public function getOneFromParent($uuid, Request $request)
    {
        $parentModel = static::$parentModel;
        $parentResource = $parentModel::findOrFail($uuid);

        // Authorize ability to view children models for parent
        $this->authorizeUserAction($this->parentAbilitiesRequired['view'], $parentResource);

        $model = static::$model;
        $resourceRelationName = Helpers::modelRelationName($model, 'one');

        // Form model's with relations for parent query
        $withArray = [];
        foreach ($model::$itemWith as $modelRelation) {
            $withArray[] = $resourceRelationName . '.' . $modelRelation;
        }

        $withArray = array_merge([$resourceRelationName], $withArray);
        $parentResource = $parentResource->where($parentResource->getKeyName(), '=', $parentResource->getKey())->with($withArray)->first();

        $resource = $parentResource->getRelationValue($resourceRelationName);

        // Make sure it exists, and if so, authorize view action
        if ($resource == null) {
            throw new NotFoundHttpException('Can not find a "' . $resourceRelationName . '" attached to this ' . (new \ReflectionClass($parentModel))->getShortName());
        } else {
            // Authorize ability to view this model
            $this->authorizeUserAction('view', $resource);
        }

        return $this->response->item($resource, $this->getTransformer());
    }

    /**
     * Request to retrieve a single item of this resource
     *
     * @param  string  $parentUuid  UUID of the parent resource
     * @param  string  $uuid  UUID of the resource
     * @return \Dingo\Api\Http\Response
     *
     * @throws HttpException
     */
    public function get($parentUuid, $uuid)
    {
        // Check parent exists
        $parentModel = static::$parentModel;
        $parentResource = $parentModel::findOrFail($parentUuid);

        // Authorize ability to view children model for parent
        $this->authorizeUserAction($this->parentAbilitiesRequired['view'], $parentResource);

        // Get resource
        $model = new static::$model;
        $resource = $model::with($model::getItemWith())->where($model->getKeyName(), '=', $uuid)->firstOrFail();

        // Check resource belongs to parent
        if ($resource->getAttribute($parentResource->getKeyName()) != $parentResource->getKey()) {
            throw new AccessDeniedHttpException('Resource \'' . class_basename(static::$model) . '\' with given UUID ' . $uuid . ' does not belong to ' .
                'resource \'' . class_basename(static::$parentModel) . '\' with given UUID ' . $parentUuid . '; ');
        }

        if (! $resource) {
            throw new NotFoundHttpException('Resource \'' . class_basename(static::$model) . '\' with given UUID ' . $uuid . ' not found');
        }

        // Authorize ability to create this model
        $this->authorizeUserAction('view', $resource);

        return $this->response->item($resource, $this->getTransformer());
    }

    /**
     * Request to create the child resource owned by the parent resource
     *
     * @oaram string $parentUuid Parent's UUID
     *
     * @param  Request  $request
     * @return \Dingo\Api\Http\Response
     *
     * @throws HttpException
     */
    public function post($parentUuid, Request $request)
    {
        // Check parent exists
        $parentModel = static::$parentModel;
        $parentResource = $parentModel::findOrFail($parentUuid);

        // Authorize ability to create children model for parent
        $this->authorizeUserAction($this->parentAbilitiesRequired['create'], $parentResource);

        // Authorize ability to create this model
        $this->authorizeUserAction('create');

        $requestData = $request->input();
        $model = new static::$model;

        // Validate
        $this->restfulService->validateResource($model, $requestData);

        // Set parent key in request data
        $resource = new $model($requestData);

        $parentRelation = $parentResource->{$this->getChildRelationNameForParent($parentResource, static::$model)}();
        $resource->{$parentRelation->getForeignKeyName()} = $parentUuid;

        // Create model in DB
        $resource = $this->restfulService->persistResource($resource);

        // Retrieve full model
        $resource = $model::with($model::getItemWith())->where($model->getKeyName(), '=', $resource->getKey())->first();

        if ($this->shouldTransform()) {
            $response = $this->response->item($resource, $this->getTransformer())->setStatusCode(201);
        } else {
            $response = $resource;
        }

        return $response;
    }

    /**
     * Request to update the specified child resource
     *
     * @param  string  $parentUuid  UUID of the parent resource
     * @param  string  $uuid  UUID of the child resource
     * @param  Request  $request
     * @return \Dingo\Api\Http\Response
     *
     * @throws HttpException
     */
    public function patch($parentUuid, $uuid, Request $request)
    {
        // Check parent exists
        $parentModel = static::$parentModel;
        $parentResource = $parentModel::findOrFail($parentUuid);

        // Authorize ability to update children models for parent
        $this->authorizeUserAction($this->parentAbilitiesRequired['update'], $parentResource);

        // Get resource
        $model = new static::$model;
        $resource = static::$model::findOrFail($uuid);

        // Check resource belongs to parent
        if ($resource->getAttribute($parentResource->getKeyName()) != $parentResource->getKey()) {
            throw new AccessDeniedHttpException('Resource \'' . class_basename(static::$model) . '\' with given UUID ' . $uuid . ' does not belong to ' .
                'resource \'' . class_basename(static::$parentModel) . '\' with given UUID ' . $parentUuid . '; ');
        }

        $this->authorizeUserAction('update', $resource);

        // Validate the resource data with the updates
        $this->restfulService->validateResourceUpdate($resource, $request->input());

        // Patch model
        $this->restfulService->patch($resource, $request->input());

        // Get updated resource
        $resource = $model::with($model::getItemWith())->where($model->getKeyName(), '=', $uuid)->first();

        if ($this->shouldTransform()) {
            $response = $this->response->item($resource, $this->getTransformer());
        } else {
            $response = $resource;
        }

        return $response;
    }

    /**
     * Deletes a child resource by UUID
     *
     * @param  string  $parentUuid  UUID of the parent resource
     * @param  string  $uuid  UUID of the child resource
     * @return \Dingo\Api\Http\Response
     *
     * @throws HttpException
     */
    public function delete($parentUuid, $uuid)
    {
        // Check parent exists
        $parentModel = static::$parentModel;
        $parentResource = $parentModel::findOrFail($parentUuid);

        // Authorize ability to delete children model for parent
        $this->authorizeUserAction($this->parentAbilitiesRequired['delete'], $parentResource);

        $resource = static::$model::findOrFail($uuid);

        $this->authorizeUserAction('delete', $resource);

        // Check resource belongs to parent
        if ($resource->getAttribute($parentResource->getKeyName()) != $parentResource->getKey()) {
            throw new AccessDeniedHttpException('Resource \'' . class_basename(static::$model) . '\' with given UUID ' . $uuid . ' does not belong to ' .
                'resource \'' . class_basename(static::$parentModel) . '\' with given UUID ' . $parentUuid . '; ');
        }

        $deletedCount = $resource->delete();

        if ($deletedCount < 1) {
            throw new NotFoundHttpException('Could not find a resource with that UUID to delete');
        }

        return $this->response->noContent()->setStatusCode(204);
    }
}
