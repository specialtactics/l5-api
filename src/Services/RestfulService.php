<?php

namespace Specialtactics\L5Api\Services;

use Validator;
use Config;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Dingo\Api\Exception\StoreResourceFailedException;
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
     * @var string $model The Model Class name
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
     * @param string|null $model
     * @return $this
     */
    public function setModel($model) {
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
        return $model->update($request->input());
    }

    /**
     * Create model in the database
     *
     * @param $model
     * @param $data
     * @return mixed
     */
    public function persistResource(RestfulModel $resource) {
        try {
            $resource->save();
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
            $errorMessage = 'Unexpected error trying to store this resource.';

            if (Config::get('api.debug') === true) {
                $errorMessage .= ' ' . $e->getMessage();
            }

            throw new UnprocessableEntityHttpException($errorMessage);
        }

        return $resource;
    }
}