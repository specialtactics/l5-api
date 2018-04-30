<?php

namespace Specialtactics\L5Api\Http\Controllers;

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
     * @var $model \Illuminate\Database\Eloquent\Model;
     */
    public static $model = null;
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
        $model = static::$model;
        $objects = $model::all();

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
