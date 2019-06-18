<?php

namespace Specialtactics\L5Api\Tests\Api\Get;

use Specialtactics\L5Api\Tests\TestCase;

class GetTest extends TestCase
{
    public function testGet()
    {





        $jsonResponse = $this->actingAsAdmin()
            ->json('GET', '/users');
        var_dump($jsonResponse->status());



        //$jsonResponse->assertStatus(200);



    }
}
