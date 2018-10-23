<?php

namespace Specialtactics\L5Api\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;
use Specialtactics\L5Api\Models\RestfulModel;
use Dingo\Api\Routing\Helpers;
use Specialtactics\L5Api\Services\RestfulService;
use Specialtactics\L5Api\Transformers\RestfulTransformer;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Dingo\Api\Exception\StoreResourceFailedException;

class RestfulController extends Controller
{
	use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
	use Helpers;
	use Features\RestfulControllerTrait;
	use Features\AuthorizesUserActionsOnModelsTrait;

	/**
	 * @var \Specialtactics\L5Api\Services\RestfulService
	 */
	protected $restfulService = null;

	/**
	 * Specify the model that you want to be associated with this controller. This is the primary model that
	 * the controller deals with
	 *
	 * @var RestfulModel $model
	 */
	public static $model = null;

	/**
	 * Usually a transformer will be associated with a model, however if you don't specify a model or with to
	 * override the transformer at a controller level (for example if it's a controller for a dashboard resource), then
	 * you can do so by specifying a transformer here
	 *
	 * @var null|RestfulTransformer The transformer this controller should use
	 */
	public static $transformer = null;

	/**
	 * RestfulController constructor.
	 *
	 * @param RestfulService $restfulService
	 */
	public function __construct(RestfulService $restfulService)
	{
		$this->restfulService = $restfulService->setModel(static::$model);
	}

	/**
	 * Request to retrieve a collection of all items of this resource
	 *
	 * @return \Dingo\Api\Http\Response
	 */
	public function getAll()
	{
		$model = new static::$model;

		$query = QueryBuilder::for(static::$model::query())
		                     ->allowedIncludes($model::$allowedIncludes)
		                     ->defaultSort($model::$defaultSort)
		                     ->allowedSorts($model::$allowedSorts)
		                     ->allowedFields($model::$allowedFields)
		                     ->allowedFilters($model::allowedFilters)
		                     ->jsonPaginate();

		$this->qualifyCollectionQuery($query);

		return $this->response->paginator($query, $this->getTransformer());
	}

	/**
	 * Request to retrieve a single item of this resource
	 *
	 * @param string $uuid UUID of the resource
	 * @return \Dingo\Api\Http\Response
	 * @throws HttpException
	 */
	public function get($uuid)
	{
		$model = new static::$model;

		$resource = $model::with($model::$localWith)->where($model->getKeyName(), '=', $uuid)->first();

		if ( ! $resource) {
			throw new NotFoundHttpException('Resource \'' . class_basename(static::$model) . '\' with given UUID ' . $uuid . ' not found');
		}

		$this->authorizeUserAction('view', $resource);

		return $this->response->item($resource, $this->getTransformer());
	}

	/**
	 * Request to create a new resource
	 *
	 * @param Request $request
	 * @return \Dingo\Api\Http\Response
	 * @throws HttpException|QueryException
	 */
	public function post(Request $request)
	{
		$this->authorizeUserAction('create');

		$model = new static::$model;

		// Validation
		$validator = Validator::make($request->input(), $model->getValidationRules(), $model->getValidationMessages());

		if ($validator->fails()) {
			throw new StoreResourceFailedException('Could not create resource.', $validator->errors());
		}

		$resource = $this->restfulService->persistResource(new $model($request->input()));

		// Retrieve full model
		$resource = $model::with($model::$localWith)->where($model->getKeyName(), '=', $resource->getKey())->first();

		if ($this->shouldTransform()) {
			$response = $this->response->item($resource, $this->getTransformer())->setStatusCode(201);
		} else {
			$response = $resource;
		}

		return $response;
	}

	public function put($object)
	{
	}

	/**
	 * Request to update the specified resource
	 *
	 * @param string $uuid UUID of the resource
	 * @param Request $request
	 * @return \Dingo\Api\Http\Response
	 * @throws HttpException
	 */
	public function patch($uuid, Request $request)
	{
		$model = static::$model::findOrFail($uuid);

		$this->authorizeUserAction('update', $model);

		// Validate the resource data with the updates
		$validator = Validator::make($request->input(), array_intersect_key($model->getValidationRules(), $request->input()), $model->getValidationMessages());

		if ($validator->fails()) {
			throw new StoreResourceFailedException('Could not update resource with UUID "'.$model->getKey().'".', $validator->errors());
		}

		// Patch model
		$this->restfulService->patch($model, $request);

		if ($this->shouldTransform()) {
			$response = $this->response->item($model, $this->getTransformer());
		} else {
			$response = $model;
		}

		return $response;
	}

	/**
	 * Deletes a resource by UUID
	 *
	 * @param string $uuid UUID of the resource
	 * @return \Dingo\Api\Http\Response
	 * @throws NotFoundHttpException
	 */
	public function delete($uuid)
	{
		$model = static::$model::findOrFail($uuid);

		$this->authorizeUserAction('delete', $model);

		$deletedCount = $model->delete();

		if ($deletedCount < 1) {
			throw new NotFoundHttpException('Could not find a resource with that UUID to delete');
		}

		return $this->response->noContent()->setStatusCode(204);
	}
}