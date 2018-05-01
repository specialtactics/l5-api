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

        /**
         * Get the relations for this object and transform then
         */
        foreach($model->getRelations() as $relationKey => $relation) {

            // Skip Pivot
            if($relation instanceof \Illuminate\Database\Eloquent\Relations\Pivot) {
                continue;
            }

            // Transform Collection
            else if ($relation instanceof \Illuminate\Database\Eloquent\Collection) {
                if( count($relation->getIterator()) > 0) {
                    $relationObject = $relation->first();
                    $class = get_class($relationObject);
                    $relationTransformerRaw = $class.'Transformer';

                    $relationTransformer = new $relationTransformerRaw;
                    if($model->$relationKey) {
                        foreach($relation->getIterator() as $key => $item) {
                            //replace the entity in the object transformed, because it probably will have been transformed
                            //need to use the specific transformer
                            $objectTransformed[camel_case($relationKey)][$key] = $relationTransformer->transform($item);
                        }
                    }
                }
            } else if ($relation instanceof RestfulModel) {
                // Get transformer of relation model
                $relationTransformer = $relation::$transformer;
                $transformer = is_null($relationTransformer) ? new RestfulTransformer() : new $relationTransformer;

                if (array_key_exists($relationKey, $model)) {
                    $objectTransformed[camel_case($relationKey)] = $transformer->transform($model->$relationKey);
                }
            }
        }

        return $transformed;
    }
}