<?php

namespace Specialtactics\L5Api\Models;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class Builder.
 *
 * This changes some default functionality to be more API-friendly
 */
class Builder extends \Illuminate\Database\Eloquent\Builder
{
    /**
     * Wrapper to throw a 404 instead of a 500 on model not found.
     *
     * @param mixed $id
     * @param array $columns
     *
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    public function findOrFail($id, $columns = ['*'])
    {
        try {
            $resource = parent::findOrFail($id, $columns);
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Resource \''.class_basename($e->getModel()).'\' with given UUID '.$id.' not found');
        }

        return $resource;
    }
}
