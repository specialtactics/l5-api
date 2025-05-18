<?php

namespace Specialtactics\L5Api\Tests\Unit;

use App\Http\Controllers\ForumController;
use Specialtactics\L5Api\Enums\PaginationType;
use Specialtactics\L5Api\Tests\AppTestCase;

class PaginationTest extends AppTestCase
{
    public function testLengthAwarePagination()
    {
        $jsonResponse = $this->actingAsAdmin()
            ->json('GET', '/forums');

        $jsonResponse->assertStatus(200);
        $response = $jsonResponse->decodeResponseJson();

        $this->assertEquals(1, $response['meta']['pagination']['total']);
        $this->assertEquals(1, $response['meta']['pagination']['totalPages']);
    }

    public function testSimplePagination()
    {
        $mockForumController = $this->partialMock(ForumController::class);
        $mockForumController->paginationType = PaginationType::SIMPLE;

        $jsonResponse = $this->actingAsAdmin()
            ->json('GET', '/forums');

        $jsonResponse->assertStatus(200);
        $response = $jsonResponse->decodeResponseJson();

        // League's Serealiser contract needs to return something, so at the moment it's zero for simple pagination
        $this->assertEquals(0, $response['meta']['pagination']['total']);
        $this->assertEquals(0, $response['meta']['pagination']['totalPages']);
    }
}
