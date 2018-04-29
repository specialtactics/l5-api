<?php

namespace Specialtactics\L5Api\Transformers;

use League\Fractal\TransformerAbstract;
use Specialtactics\L5Api\Models\RestfulModel;

class RestfulTransformer extends TransformerAbstract
{
    /**
     * Transform an eloquent object into a jsonable array
     *
     * @param RestfulModel $model
     * @return array
     */
    public function transform(RestfulModel $model)
    {
        $responseTransformed = $model->toArray();

        return $responseTransformed;
    }
}