<?php

namespace Specialtactics\L5Api\Tests\App\Models;

use Specialtactics\L5Api\Tests\App\Transformers\BaseTransformer;

class Post extends BaseModel
{
    /**
     * @var string UUID key
     */
    public $primaryKey = 'post_id';

    /**
     * @var array Relations to load implicitly by Restful controllers
     */
    public static $localWith = ['topic', 'author'];

    /**
     * @var null|BaseTransformer The transformer to use for this model, if overriding the default
     */
    public static $transformer = null;

    /**
     * @var array The attributes that are mass assignable.
     */
    protected $fillable = ['topic_id', 'author_id', 'content'];

    /**
     * @var array The attributes that should be hidden for arrays and API output
     */
    protected $hidden = [];

    /**
     * Return the validation rules for this model
     *
     * @return array Rules
     */
    public function getValidationRules()
    {
        return [
            'content' => 'required',
        ];
    }

    /**
     * Boot the model
     *
     * Add various functionality in the model lifecycle hooks
     */
    public static function boot()
    {
        parent::boot();

        // Add functionality for creating a model
        static::creating(function (Post $model) {
            $model->author_id = auth()->user()->getKey();
        });
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id', 'user_id');
    }

    public function topic()
    {
        return $this->belongsTo(Topic::class, 'topic_id', 'topic_id');
    }
}
