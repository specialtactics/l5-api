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
use Validator;
use Dingo\Api\Routing\Helpers;
use Specialtactics\L5Api\Transformers\RestfulTransformer;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Dingo\Api\Exception\StoreResourceFailedException;

class RestfulController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    use Helpers;

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
     * Usually a transformer will be associated with a model, however if you don't specify a model or with to
     * override the transformer at a controller level (for example if it's a controller for a dashboard resource), then
     * you can do so by specifying a transformer here
     *
     * @var null|BaseTransformer The transformer this controller should use
     */
    public static $transformer = null;

    /**
     * RestfulController constructor.
     *
     * @param RestfulService $restfulService
     */
    public function __construct(RestfulService $restfulService)
    {
        $this->restfulService = $restfulService;

        // Figure out whether we're using the base controller or a specific one
        if ( is_null(static::$transformer)) {
            static::$transformer = RestfulTransformer::class;
        }
    }

    /**
     * Request to retrieve a collection of all items of this resource
     *
     * @return \Dingo\Api\Http\Response
     */
    public function getAll()
    {
        $model = new static::$model;
        $resources = $model::with($model::$localWith)->get();

        return $this->response->collection($resources, $this->getTransformer());
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

        $resource = $model::with($model::$localWith)->where($model->getUuidKeyName(), '=', $uuid)->first();

        if ( ! $resource) {
            throw new NotFoundHttpException('Resource \'' . class_basename(static::$model) . '\' with given UUID ' . $uuid . ' not found');
        }

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
        $model = new static::$model;

        // Validation
        $validator = Validator::make($request->request->all(), array_intersect_key($model->getValidationRules(), $model->getValidationMessages()));

        if ($validator->fails()) {
            throw new StoreResourceFailedException('Could not create resource.', $validator->errors());
        }

        try {
            $resource = $model::create($request->request->all());
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
            throw new UnprocessableEntityHttpException('Unexpected error trying to store this resource');
        }

        // Put UUID as the first attribute
        $resource->orderAttributesUuidFirst();

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

        // Validate the resource data with the updates
        $validator = Validator::make($request->request->all(), array_intersect_key($model->getValidationRules(), $model->getValidationMessages()));

        if ($validator->fails()) {
            throw new StoreResourceFailedException('Could not update resource with UUID "'.$model->getUuidKey().'".', $validator->errors());
        }

        // Patch model
        $resource = $this->restfulService->patch($model, $request);

        return $this->response->item($resource, $this->getTransformer());
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
        $model = static::$model;

        $this->restfulService->delete($model, $uuid);

        return $this->response->noContent()->setStatusCode(204);
    }

    /**
     * Figure out which transformer to use
     *
     * @return RestfulTransformer
     */
    protected function getTransformer()
    {
        $transformer = null;

        // Check if controller specifies a resource
        if ( ! is_null(static::$transformer)) {
            $transformer = static::$transformer;
        }

        // Otherwise, check if model is specified
        else {
            // If it is, check if the controller's model specifies the transformer
            if ( ! is_null(static::$model)) {
                // If it does, use it
                if ( ! is_null((static::$model)::$transformer)) {
                    $transformer = (static::$model)::$transformer;
                }
            }
        }

        // This is the default transformer, if one is not specified
        if (is_null($transformer)) {
            $transformer = BaseTransformer::class;
        }

        return new $transformer;
    }

    /**
     * Check if this request's body input is a collection of objects or not
     *
     * @return bool
     */
    protected function isRequestBodyACollection(Request $request)
    {
        $input = $request->request->all();

        // Check that the top-level of the body is an array
        if (is_array($input) && sizeof($input) > 0) {

            // Check if the first element is an array (json object)
            $firstChild = array_shift($input);

            if (is_array($firstChild)) {
                return true;
            }
        }

        return false;
    }

    /**
     * This method determines whether the resource returned should undergo transformation or not.
     * The reason is, sometimes it is useful to return the untransformed resource (for example - for internal calls)
     *
     * @return bool
     */
    protected function shouldTransform() {
        // If we are not called by this function, then we are not called by the router
        if (debug_backtrace()[2]['function'] != 'call_user_func_array') {
            return false;
        }

        return true;
    }
}
