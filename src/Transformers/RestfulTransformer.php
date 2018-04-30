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
        $transformed = $model->toArray();

        /**
         * Format all dates as Iso8601 strings, this includes the created_at and updated_at columns
         */
        foreach($model->getDates() as $dateColumn) {
            if(!empty($model->$dateColumn)) {
                $transformed[$dateColumn] = $model->$dateColumn->toIso8601String();
            }
        }

        /**
         * Transform all keys to CamelCase, recursively
         */
        camel_case_array($transformed);

        return $transformed;
    }
}