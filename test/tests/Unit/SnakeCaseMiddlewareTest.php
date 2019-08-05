<?php

namespace Specialtactics\L5Api\Tests\Unit;

use Dingo\Api\Http\Request;
use Mockery;
use Specialtactics\L5Api\Http\Middleware\SnakeCaseInputParameterKeys;
use Specialtactics\L5Api\Tests\BaseTestCase;

class SnakeCaseMiddlewareTest extends BaseTestCase
{
    /**
     * Test that we are not snake_casing keys which are all uppercase
     *
     * @test
     */
    public function dontTransformAllUpercaseKeys()
    {
        // Setup request
        $content = [
            'keyShouldTransform' => true,
            'AUD/USD' => '0.80',
            'AUD' => '0.75'
        ];

        $request = Request::create('/test', 'POST', [], [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($content));

        $snakeCaseMiddleware = new SnakeCaseInputParameterKeys;
        $snakeCaseMiddleware->handle($request, function($request) {});

        $input = $request->input();

        $this->assertArrayHasKey('key_should_transform', $input);
        $this->assertArrayHasKey('AUD/USD', $input);
        $this->assertArrayHasKey('AUD', $input);
    }
}
