<?php

namespace Specialtactics\L5Api\Http\Controllers;

use App\Models\BaseModel;
use App\Transformers\BaseTransformer;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\PackageManifest;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Dingo\Api\Routing\Helpers;
use Specialtactics\L5Api\Transformers\RestfulTransformer;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RestfulController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    use Helpers;

    /**
     * @var null
     */
    protected $restfulService = null;

    /**
     * Specify the model that you want to be associated with this controller. This is the primary model that
     * the controller deals with
     *
     * @var $model \App\Models\BaseModel
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
     */
    public function __construct() {
        // Figure out whether we're using the base controller or a specific one
        if ( is_null(static::$transformer)) {
            static::$transformer = RestfulTransformer::class;
        }
    }

    /**
     * Request to retrieve a collection of all items for this resource
     *
     * @return \Dingo\Api\Http\Response
     */
    public function getAll() {
        $model = new static::$model;
        $resources = $model::with($model::$localWith)->get();

        return $this->response->collection($resources, $this->getTransformer());
    }

    /**
     * Request to retrieve of a single item of this resource
     *
     * @param $uuid string UUID of the resource
     * @return \Dingo\Api\Http\Response
     * @throws HttpException
     */
    public function get($uuid) {
        $model = new static::$model;

        $resource = $model::with($model::$localWith)->where($model->getUuidKeyName(), '=', $uuid)->first();

        if ( ! $resource) {
            throw new NotFoundHttpException('Resource \'' . class_basename(static::$model) . '\' with given UUID ' . $uuid . ' not found');
        }

        return $this->response->item($resource, $this->getTransformer());
    }

    /**
     * Request for POST to create a new resource
     *
     * @param $object
     */
    public function post($object) {

    }

    public function put($object) {

    }

    /**
     * Request to update the specified resource
     *
     * @param $uuid string UUID of the resource
     * @return \Dingo\Api\Http\Response
     * @throws HttpException
     */
    public function patch($uuid)
    {
        $model = new static::$model;

        /** @var BaseModel $resource */
        $resource = $model::findOrFail($uuid);


        // @todo
    }

    public function delete($uuid) {

    }

    /**
     * Figure out which transformer to use
     *
     * @return RestfulTransformer
     */
    protected function getTransformer() {
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
}
