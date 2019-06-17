<?php

namespace Specialtactics\L5Api\Tests\App\Http\Controllers;

use Illuminate\Http\Request;
use Specialtactics\L5Api\Tests\App\Models\Post;
use Specialtactics\L5Api\Tests\App\Models\Topic;

class PostController extends ChildController
{
    /**
     * @var BaseModel The primary model associated with this controller
     */
    public static $model = Post::class;

    /**
     * @var BaseModel The parent model of the model, in the case of a child rest controller
     */
    public static $parentModel = Topic::class;

    /**
     * @var null|BaseTransformer The transformer this controller should use, if overriding the model & default
     */
    public static $transformer = null;

}
