<?php

namespace App\Models;

use App\Transformers\BaseTransformer;

class Topic extends BaseModel
{
    /**
     * @var string UUID key
     */
    public $primaryKey = 'topic_id';

    /**
     * @var array Relations to load implicitly by Restful controllers
     */
    public static $localWith = ['author', 'forum', 'posts'];

    /**
     * @var null|BaseTransformer The transformer to use for this model, if overriding the default
     */
    public static $transformer = null;

    /**
     * @var array The attributes that are mass assignable.
     */
    protected $fillable = ['title', 'forum_id', 'author_id'];

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
            'title' => 'required|string',
        ];
    }

    public function posts()
    {
        return $this->hasMany(Post::class, 'topic_id', 'topic_id');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id', 'user_id');
    }

    public function forum()
    {
        return $this->belongsTo(Forum::class, 'forum_id', 'forum_id');
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
        static::creating(function (Topic $model) {
            $model->author_id = auth()->user()->getKey();
        });
    }
}
