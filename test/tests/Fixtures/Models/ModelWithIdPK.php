<?php

namespace Specialtactics\L5Api\Tests\Fixtures\Models;

use Specialtactics\L5Api\Models\RestfulModel;

class ModelWithIdPK extends RestfulModel
{
    /**
     * @var string UUID key
     */
    public $primaryKey = 'id';

    /**
     * @var array The attributes that are mass assignable.
     */
    protected $fillable = ['example_attribute'];
}
