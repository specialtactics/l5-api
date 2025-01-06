<?php

namespace App\Models\Dates;

use App\Models\BaseModel;
use App\Transformers\BaseTransformer;
use Illuminate\Database\Eloquent\SoftDeletes;

class ModelWithDates extends BaseModel
{
    use SoftDeletes;

    /**
     * @var string UUID key
     */
    public $primaryKey = 'model_with_dates_id';

    /**
     * @var array The attributes that are mass assignable.
     */
    protected $fillable = ['title', 'processed_at', 'scheduled_at', 'counted_at'];

    public $dates = ['created_at', 'updated_at', 'deleted_at', 'processed_at'];

    public $casts = [
        'scheduled_at' => 'date',
        'counted_at' => 'datetime',
    ];

    /**
     * Return the validation rules for this model
     *
     * @return array Rules
     */
    public function getValidationRules()
    {
        return [
            'title' => 'required|string|unique:forums',
        ];
    }
}
