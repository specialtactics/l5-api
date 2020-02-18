<?php

namespace Specialtactics\L5Api\Tests\Fixtures\Models;

use Specialtactics\L5Api\Models\RestfulModel;

class ModelWithCasts extends RestfulModel
{
    /**
     * @var string UUID key
     */
    public $primaryKey = 'model_with_casts_id';

    /**
     * @var array The attributes that are mass assignable.
     */
    protected $fillable = ['model_with_casts_id', 'array_attribute'];

    /**
     * @var array Casts
     */
    protected $casts = [
        'array_attribute' => 'array'
    ];
}
