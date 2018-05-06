<?php

namespace Specialtactics\L5Api\Services;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
     * Deletes resources of the given model and uuid(s)
     *
     * @param $model string Model class name
     * @param $uuid string|array The UUID(s) of the models to remove
     * @return mixed
     */
    public function delete($model, $uuid) {
        $deletedCount = $model::destroy($uuid);

        if ($deletedCount < 1) {
            throw new NotFoundHttpException('Could not find a resource with that UUID to delete');
        }

        return $deletedCount;
    }
}