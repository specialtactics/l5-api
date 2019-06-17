<?php

namespace Specialtactics\L5Api\Tests\App\Services;

use Specialtactics\L5Api\Tests\App\Models\Topic;
use Specialtactics\L5Api\Tests\App\Models\Post;

class PostService
{
    /**
     * @var \Specialtactics\L5Api\Tests\App\Services\RestfulService
     */
    protected $restfulService = null;

    /**
     * PostService constructor.
     * @param RestfulService $restfulService
     */
    public function __construct(RestfulService $restfulService)
    {
        $this->restfulService = $restfulService;
    }

    /**
     * Creates a new post, and returns it
     *
     * @param Topic $topic
     * @param array $data
     * @return Post $post
     */
    public function createPost(Topic $topic, array $data)
    {
        $fillData = $data + [$topic->getKeyName() => $topic->getKey()];

        $post = new Post;

        $this->restfulService->validateResource($post, $fillData);

        $this->restfulService->persistResource($post->fill($fillData));

        return $post;
    }
}