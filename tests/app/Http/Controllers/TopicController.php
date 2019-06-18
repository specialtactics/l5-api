<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Services\RestfulService;
use Illuminate\Http\Request;
use App\Models\Topic;
use App\Models\Forum;
use App\Services\PostService;

class TopicController extends ChildController
{
    /**
     * @var BaseModel The primary model associated with this controller
     */
    public static $model = Topic::class;

    /**
     * @var BaseModel The parent model of the model, in the case of a child rest controller
     */
    public static $parentModel = Forum::class;

    /**
     * @var null|BaseTransformer The transformer this controller should use, if overriding the model & default
     */
    public static $transformer = null;

    /**
     * @var PostService|null
     */
    protected $postService = null;

    /**
     * TopicController constructor.
     * @param RestfulService $restfulService
     * @param PostService $postService
     */
    public function __construct(RestfulService $restfulService, PostService $postService)
    {
        parent::__construct($restfulService);

        $this->postService = $postService;
    }

    /**
     * @param $parentUuid
     * @param Request $request
     * @return \Dingo\Api\Http\Response
     */
    /*
    public function post($parentUuid, Request $request)
    {
        $topic = parent::post($parentUuid, $request);

        $this->api()->post('/topics/' . $topic->getKey() . '/posts', $request->input('post'));

        $topic->refresh();

        return $this->response->item($topic, $this->getTransformer())->setStatusCode(201);
    }
    */

    /*
    public function post($parentUuid, Request $request)
    {
        $topic = parent::post($parentUuid, $request);

        $this->postService->createPost($topic, $request->input('post'));

        $topic->refresh();

        return $this->response->item($topic, $this->getTransformer())->setStatusCode(201);
    }
    */
}
