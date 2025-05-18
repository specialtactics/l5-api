<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Forum;
use Specialtactics\L5Api\Enums\PaginationType;

class ForumController extends Controller
{
    /**
     * @var BaseModel The primary model associated with this controller
     */
    public static $model = Forum::class;

    /**
     * @var null|BaseTransformer The transformer this controller should use, if overriding the model & default
     */
    public static $transformer = null;

    public PaginationType $paginationType = PaginationType::LENGTH_AWARE;
}
