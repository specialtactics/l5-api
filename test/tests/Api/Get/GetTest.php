<?php

namespace Specialtactics\L5Api\Tests\Api\Get;

use Specialtactics\L5Api\Tests\AppTestCase;

class GetTest extends AppTestCase
{
    public function testGet()
    {



        $jsonResponse = $this->actingAsAdmin()
            ->json('GET', '/users');


        //var_dump($jsonResponse->status());



        $jsonResponse->assertStatus(200);



    }
}
