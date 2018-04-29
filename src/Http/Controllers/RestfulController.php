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

    public function getAll() {
        $model = static::$model;
        $objects = $model::all();

        return $this->response->collection($objects, $this->getTransformer());
    }

    public function get($uuid) {

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
