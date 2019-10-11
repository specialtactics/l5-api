<?php

namespace Specialtactics\L5Api\Http\Controllers;

use App\Services\RestfulService;
use App\Transformers\BaseTransformer;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Dingo\Api\Routing\Helpers;

class BaseRestfulController extends Controller
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
     * Usually a transformer will be associated with a model, however if you don't specify a model or with to
     * override the transformer at a controller level (for example if it's a controller for a dashboard resource), then
     * you can do so by specifying a transformer here
     *
     * @var null|BaseTransformer The transformer this controller should use
     */
    public static $transformer = null;

    /**
     * Whether to cache the getAll() function results from the DB
     * Default value of `false` disables functionality - to enable, specify the TTL in seconds
     *
     * WARNING: If you are implementing permissions logic with `qualifyCollectionQuery()`, you should not use this.
     * It is best suited to unprivileged data which changes infrequently (eg. countries, currencies, locales, etc)
     *
     * @var bool|int
     */
    public static $cacheGetAll = false;

    /**
     * RestfulController constructor.
     *
     * @param RestfulService $restfulService
     */
    public function __construct(RestfulService $restfulService)
    {
        $this->restfulService = $restfulService->setModel(static::$model);
    }
}
