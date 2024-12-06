<?php

namespace Specialtactics\L5Api\Transformers;

use DateTimeInterface;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;
use Specialtactics\L5Api\APIBoilerplate;
use Specialtactics\L5Api\Models\RestfulModel;
use Illuminate\Database\Eloquent\Model as EloquentModel;

class RestfulTransformer extends TransformerAbstract
{
    public const DATE_CAST_TYPES = ['date', 'datetime', 'immutable_date', 'immutable_datetime'];

    /**
     * @var RestfulModel The model to be transformed
     */
    protected $model = null;

    /**
     * Transform an object into a jsonable array
     *
     * @param  mixed  $model
     * @return array
     *
     * @throws \Exception
     */
    public function transform($object)
    {
        if (is_object($object)) {
            if ($object instanceof RestfulModel) {
                $transformed = $this->transformRestfulModel($object);
            } elseif ($object instanceof EloquentModel) {
                $transformed = $this->transformEloquentModel($object);
            } elseif ($object instanceof \stdClass) {
                $transformed = $this->transformStdClass($object);
            } else {
                throw new \Exception('Unexpected object type encountered in transformer');
            }
        } else {
            throw new \Exception('Unexpected object type encountered in transformer');
        }

        return $transformed;
    }

    /**
     * Transform an arbitrary stdClass
     *
     * @param  \stdClass  $object
     * @return array
     */
    public function transformStdClass($object)
    {
        $transformed = (array) $object;

        /**
         * Transform all keys to correct case, recursively
         */
        $transformed = $this->transformKeysCase($transformed);

        return $transformed;
    }

    /**
     * Transform a restful model object into a jsonable array
     *
     * @param  RestfulModel  $model
     * @return array
     */
    public function transformRestfulModel(EloquentModel $model)
    {
        $this->model = $model;

        // Begin the transformation!
        $transformed = $model->toArray();

        /**
         * Filter out attributes we don't want to expose to the API
         */
        $filterOutAttributes = $this->getFilteredOutAttributes();

        $transformed = array_filter($transformed, function ($key) use ($filterOutAttributes) {
            return ! in_array($key, $filterOutAttributes);
        }, ARRAY_FILTER_USE_KEY);

        /*
         * Format all dates as Iso8601 strings, this includes timestamped columns
         */
        foreach ($this->getModelDateFields($model) as $dateColumn) {
            if (! empty($model->$dateColumn) && ! in_array($dateColumn, $filterOutAttributes)) {
                if ($model->$dateColumn instanceof DateTimeInterface) {
                    $transformed[$dateColumn] = Carbon::instance($model->$dateColumn)->toIso8601String();
                } else {
                    try {
                        $transformed[$dateColumn] = Carbon::parse($model->$dateColumn)->toIso8601String();
                    } catch (\Error $e) {
                        $transformed[$dateColumn] = $model->$dateColumn;
                    }
                }
            }
        }

        /*
         * Primary Key transformation - all PKs to be called "id"
         */
        unset($transformed[$model->getKeyName()]);

        $transformed = array_merge(
            ['id' => $model->getKey()],
            $transformed
        );

        /*
         * Transform the model keys' case
         */
        $transformed = $this->transformKeysCase($transformed);

        /**
         * Get the relations for this object and transform them
         */
        $transformed = $this->transformRelations($transformed);

        return $transformed;
    }

    /**
     * At the moment, there is no difference in implementation between this and the more specific Restfulmodel,
     * however in the future there may be
     *
     * @param  EloquentModel  $model
     * @return array
     */
    public function transformEloquentModel(EloquentModel $model)
    {
        return $this->transformRestfulModel($model);
    }

    /**
     * Transform the keys of the object to the correct case as required
     *
     * @param  array  $transformed
     * @return array $transformed
     */
    protected function transformKeysCase(array $transformed)
    {
        // Get the config for how many levels of keys to transform
        $levels = config('api.formatsOptions.transform_keys_levels', null);

        /**
         * Transform all keys to required case, to the specified number of levels (by default, infinite)
         */
        $transformed = APIBoilerplate::formatKeyCaseAccordingToResponseFormat($transformed, $levels);

        /*
         * However, if the level is 1, we also want to transform the first level of keys in a json field which has been cast to array
         */
        if ($levels == 1 && ! is_null($this->model)) {
            // Go through casted atrributes, figuring out what their new key name is to access them
            foreach ($this->model->getCasts() as $fieldName => $castType) {
                if ($castType == 'array') {
                    $fieldNameFormatted = APIBoilerplate::formatKeyCaseAccordingToResponseFormat($fieldName);

                    // Transform the keys of the array attribute
                    if (array_key_exists($fieldNameFormatted, $transformed) && ! empty($transformed[$fieldNameFormatted])) {
                        $transformed[$fieldNameFormatted] = APIBoilerplate::formatKeyCaseAccordingToResponseFormat($transformed[$fieldNameFormatted]);
                    }
                }
            }
        }

        return $transformed;
    }

    /**
     * Formats case of the input array or scalar to desired case
     *
     * @deprecated
     *
     * @param  array|string  $input
     * @param  int|null  $levels  How many levels of an array keys to transform - by default recurse infiniately (null)
     * @return array $transformed
     */
    protected function formatKeyCase($input, $levels = null)
    {
        return APIBoilerplate::formatKeyCaseAccordingToResponseFormat($input, $levels);
    }

    /**
     * Filter out some attributes immediately
     *
     * Some attributes we never want to expose to an API consumer, for security and separation of concerns reasons
     * Feel free to override this function as necessary
     *
     * @return array Array of attributes to filter out
     */
    protected function getFilteredOutAttributes()
    {
        $filterOutAttributes = array_merge(
            $this->model->getHidden(),
            [
                $this->model->getKeyName(),
                'deleted_at',
            ]
        );

        return array_unique($filterOutAttributes);
    }

    /**
     * Do relation transformations
     *
     * @param  array  $transformed
     * @return array $transformed
     */
    protected function transformRelations(array $transformed)
    {
        // Iterate through all relations
        foreach ($this->model->getRelations() as $relationKey => $relation) {
            $transformedRelationKey = APIBoilerplate::formatKeyCaseAccordingToResponseFormat($relationKey);

            // Skip Pivot
            if ($relation instanceof \Illuminate\Database\Eloquent\Relations\Pivot) {
                continue;
            }

            // Transform Collection
            elseif ($relation instanceof \Illuminate\Database\Eloquent\Collection) {
                if (count($relation->getIterator()) > 0) {
                    $relationModel = $relation->first();
                    $relationTransformer = $relationModel::getTransformer();

                    // Transform related model collection
                    if ($this->model->$relationKey) {
                        // Create empty array for relation
                        $transformed[$transformedRelationKey] = [];

                        foreach ($relation->getIterator() as $key => $relatedModel) {
                            // Replace the related models with their transformed selves
                            $transformedRelatedModel = $relationTransformer->transform($relatedModel);

                            // We don't really care about pivot information at this stage
                            if (isset($transformedRelatedModel['pivot'])) {
                                unset($transformedRelatedModel['pivot']);
                            }

                            // Add transformed model to relation array
                            $transformed[$transformedRelationKey][] = $transformedRelatedModel;
                        }
                    }
                }
            }

            // Transformed related model
            elseif ($relation instanceof RestfulModel) {
                // Get transformer of relation model
                $relationTransformer = $relation::getTransformer();

                if ($this->model->$relationKey) {
                    $transformed[$transformedRelationKey] = $relationTransformer->transform($this->model->$relationKey);
                }
            }
        }

        return $transformed;
    }

    protected function getModelDateFields(EloquentModel $model): array
    {
        $dateFields = [];

        foreach ($model->getDates() as $dateColumn) {
            $dateFields[] = $dateColumn;
        }

        // For previous versions of Eloquent, dates were stored as arrays
        if ($model->dates && is_array($model->dates)) {
            $dateFields = array_merge($model->dates, $dateFields);
        }

        foreach ($model->getCasts() as $field => $castType) {
            if (in_array($castType, static::DATE_CAST_TYPES)) {
                $dateFields[] = $field;
            }
        }

        return array_unique($dateFields);
    }
}
