<?php

namespace Specialtactics\L5Api\Services;

use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Dingo\Api\Exception\StoreResourceFailedException;
use Validator;
use Specialtactics\L5Api\Models\RestfulModel;

/**
 * This class contains logic for processing restful requests
 *
 * Class RestfulService
 *
 * @package Specialtactics\L5Api\Services
 */
class RestfulService
{
    /**
     * @var RestfulModel $model;
     */
    protected $model = null;

    /**
     * RestfulService constructor.
     *
     * @param RestfulModel|null $model The model this service will be concerned with
     */
    public function __construct($model = null)
    {
        $this->model = null;
    }

    /**
     * Set model to be used in the service
     *
     * @param RestfulModel $model
     * @return $this
     */
    public function setModel(RestfulModel $model) {
        $this->model = $model;

        return $this;
    }

    /**
     * Deletes resources of the given model and uuid(s)
     *
     * @param $model string Model class name
     * @param $uuid string|array The UUID(s) of the models to remove
     * @return mixed
     */
    public function delete($model, $uuid)
    {
        $deletedCount = $model::destroy($uuid);

        if ($deletedCount < 1) {
            throw new NotFoundHttpException('Could not find a resource with that UUID to delete');
        }

        return $deletedCount;
    }

    /**
     * Patch a resource of the given model, with the given request
     *
     * @param RestfulModel $model
     * @param Request $request
     * @return bool
     * @throws HttpException
     */
    public function patch($model, $request)
    {
        return $model->update($request->request->all());
    }
}
