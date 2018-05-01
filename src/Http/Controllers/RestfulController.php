<?php

namespace Specialtactics\L5Api\Http\Controllers;

use App\Transformers\BaseTransformer;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Dingo\Api\Routing\Helpers;
use Specialtactics\L5Api\Transformers\RestfulTransformer;

class RestfulController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    use Helpers;

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
     * Request for a GET of this resource's collection
     *
     * @return \Dingo\Api\Http\Response
     */
    public function getAll() {
        $model = new static::$model;
        $objects = $model::with($model::$localWith)->get();

        return $this->response->collection($objects, new static::$transformer);
    }


    public function get($uuid) {

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

    public function patch($object) {

    }

    public function delete($uuid) {

    }

    /**
     * @return RestfulTransformer
     */
    protected function getTransformer() {
        if ( ! is_null(static::$transformer)) {
            return new static::$transformer;
        } else {
            return new RestfulTransformer;
        }
    }
}
