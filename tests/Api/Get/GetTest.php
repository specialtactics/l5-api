<?php

namespace Specialtactics\L5Api\Tests\Api\Get;

use Specialtactics\L5Api\Tests\TestCase;

class GetTest extends TestCase
{
    public function testGet()
    {

        /*
        $jsonResponse = $this->json('GET', '/users');

        $jsonResponse
            ->assertStatus(200);
        */

        /*
        $this->router->version('v1', function () {
            $this->router->get('test', function () {
                return 'foo';
            });

            $this->router->post('test', function () {
                return 'bar';
            });

            $this->router->put('test', function () {
                return 'baz';
            });

            $this->router->patch('test', function () {
                return 'yin';
            });

            $this->router->delete('test', function () {
                return 'yang';
            });
        });

        */
        //$this->assertSame('foo', $this->dispatcher->get('test'));

        $result = $this->dispatcher->get('/posts');

        var_dump($result);
    }
}
