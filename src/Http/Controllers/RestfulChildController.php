<?php

namespace Specialtactics\L5Api\Http\Controllers;

use App\Models\BaseModel;
use App\Services\RestfulService;
use App\Transformers\BaseTransformer;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Config;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Validator;
use Dingo\Api\Routing\Helpers;
use Specialtactics\L5Api\Transformers\RestfulTransformer;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Dingo\Api\Exception\StoreResourceFailedException;

class RestfulChildController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    use Helpers;
    use Features\RestfulControllerTrait;
    use Features\AuthorizesUserActionsOnModelsTrait;

    /**
     * @var \App\Services\RestfulService
     */
    protected $restfulService = null;

    /**
     * Specify the model that you want to be associated with this controller. This is the primary model that
     * the controller deals with
     *
     * @var \App\Models\BaseModel $model
     */
    public static $model = null;

    /**
     * Specify the parent model that you want to be associated with this controller. This is the parent model of the
     * primary model the controller deals with
     *
     * @var \App\Models\BaseModel $model
     */
    public static $parentModel = null;

    /**
     * Usually a transformer will be associated with a model, however if you don't specify a model or with to
     * override the transformer at a controller level (for example if it's a controller for a dashboard resource), then
     * you can do so by specifying a transformer here
     *
     * @var null|BaseTransformer The transformer this controller should use
     */
    public static $transformer = null;

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
        'view'    => 'own',
        'update'    => 'own',
        'delete'    => 'own',
    ];

    /**
     * RestfulController constructor.
     *
     * @param RestfulService $restfulService
     */
    public function __construct(RestfulService $restfulService)
    {
        $this->restfulService = $restfulService;
    }

    /**
     * Request to retrieve a collection of all items owned by the parent of this resource
     *
     * @param string $uuid
     * @param Request $request
     * @return \Dingo\Api\Http\Response
     */
    public function getAll($uuid, Request $request)
    {
        $parentModel = static::$parentModel;
        $parentResource = $parentModel::findOrFail($uuid);

        // Authorize ability to view children models for parent
        $this->authorizeUserAction($this->parentAbilitiesRequired['view'], $parentResource);

        $model = static::$model;
        $resourceRelationName = model_relation_name($model);

        // Form model's with relations for parent query
        $withArray = [];
        foreach ($model::$localWith as $modelRelation) {
            $withArray[] = $resourceRelationName . '.' . $modelRelation;
        }

        $withArray = array_merge([$resourceRelationName], $withArray);
        $parentResource = $parentResource->where($parentResource->getKeyName(), '=', $parentResource->getKey())->with($withArray)->first();

        $collection = $parentResource->getRelationValue($resourceRelationName);

        return $this->response->collection($collection, $this->getTransformer());
    }

    /**
     * Request to retrieve a single item of this resource
     *
     * @param string $parentUuid UUID of the parent resource
     * @param string $uuid UUID of the resource
     * @return \Dingo\Api\Http\Response
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
        $resource = $model::with($model::$localWith)->where($model->getUuidKeyName(), '=', $uuid)->first();

        // Check resource belongs to parent
        if ($resource->getAttribute(($parentResource->getKeyName())) != $parentResource->getKey()) {
            throw new AccessDeniedHttpException('Resource \'' . class_basename(static::$model) . '\' with given UUID ' . $uuid . ' does not belong to ' .
                'resource \'' . class_basename(static::$parentModel) . '\' with given UUID ' . $parentUuid . '; ');
        }

        if ( ! $resource) {
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
     * @param Request $request
     * @return \Dingo\Api\Http\Response
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

        $requestData = $request->request->all();
        $model = new static::$model;

        // Validation
        $validator = Validator::make($requestData, $model->getValidationRules(), $model->getValidationMessages());

        if ($validator->fails()) {
            throw new StoreResourceFailedException('Could not create resource.', $validator->errors());
        }

        try {
            $resource = $parentResource->{model_relation_name(static::$model)}()->create($requestData);
        } catch (\Exception $e) {
            // Check for QueryException - if so, we may want to display a more meaningful message, or help with
            // development debugging
            if ($e instanceof QueryException ) {
                if (stristr($e->getMessage(), 'duplicate')) {
                    throw new ConflictHttpException('That resource already exists.');
                } else if (Config::get('api.debug') === true) {
                    throw $e;
                }
            }

            // Default HTTP exception to use for storage errors
            throw new UnprocessableEntityHttpException('Unexpected error trying to store this resource: ' . $e->getMessage());
        }

        // Retrieve full model
        $resource = $model::with($model::$localWith)->where($model->getKeyName(), '=', $resource->getKey())->first();

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
     * @param string $parentUuid UUID of the parent resource
     * @param string $uuid UUID of the child resource
     * @param Request $request
     * @return \Dingo\Api\Http\Response
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
        $resource = static::$model::findOrFail($uuid);

        // Check resource belongs to parent
        if ($resource->getAttribute(($parentResource->getKeyName())) != $parentResource->getKey()) {
            throw new AccessDeniedHttpException('Resource \'' . class_basename(static::$model) . '\' with given UUID ' . $uuid . ' does not belong to ' .
                'resource \'' . class_basename(static::$parentModel) . '\' with given UUID ' . $parentUuid . '; ');
        }

        $this->authorizeUserAction('update', $resource);

        // Validate the resource data with the updates
        $validator = Validator::make($request->request->all(), array_intersect_key($resource->getValidationRules(), $request->request->all()), $resource->getValidationMessages());

        if ($validator->fails()) {
            throw new StoreResourceFailedException('Could not update resource with UUID "'.$resource->getUuidKey().'".', $validator->errors());
        }

        // Patch model
        $this->restfulService->patch($resource, $request);

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
     * @param string $parentUuid UUID of the parent resource
     * @param string $uuid UUID of the child resource
     * @return \Dingo\Api\Http\Response
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
        if ($resource->getAttribute(($parentResource->getKeyName())) != $parentResource->getKey()) {
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