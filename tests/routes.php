<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

/**
 * Welcome route - link to any public API documentation here
 */
Route::get('/', function () {
    echo 'Welcome to our API';
});


/**
 * @var $api \Dingo\Api\Routing\Router
 */
$api = app('Dingo\Api\Routing\Router');
$this->router->version('v1', ['middleware' => ['api']], function ($api) {
    /**
     * Authentication
     */
    $api->group(['prefix' => 'auth'], function ($api) {
        $api->group(['prefix' => 'jwt'], function ($api) {
            $api->get('/token', 'Specialtactics\L5Api\Tests\App\Http\Controllers\Auth\AuthController@token');
        });
    });

    /**
     * Test
     */
    $api->group(['prefix' => 'posts'], function ($api) {
        $api->get('/', 'Specialtactics\L5Api\Tests\App\Http\Controllers\PostController@getAll');
        $api->post('/', 'Specialtactics\L5Api\Tests\App\Http\Controllers\PostController@post');
    });

    $api->group(['prefix' => 'forums'], function ($api) {
        $api->get('/', 'Specialtactics\L5Api\Tests\App\Http\Controllers\ForumController@getAll');
    });


    /**
     * Authenticated routes
     */
    $api->group(['middleware' => ['api.auth']], function ($api) {
        /**
         * Authentication
         */
        $api->group(['prefix' => 'auth'], function ($api) {
            $api->group(['prefix' => 'jwt'], function ($api) {
                $api->get('/refresh', 'Specialtactics\L5Api\Tests\App\Http\Controllers\Auth\AuthController@refresh');
                $api->delete('/token', 'Specialtactics\L5Api\Tests\App\Http\Controllers\Auth\AuthController@logout');
            });

            $api->get('/me', 'Specialtactics\L5Api\Tests\App\Http\Controllers\Auth\AuthController@getUser');
        });

        /**
         * Users
         */
        $api->group(['prefix' => 'users', 'middleware' => 'check_role:admin'], function ($api) {
            $api->get('/', 'Specialtactics\L5Api\Tests\App\Http\Controllers\UserController@getAll');
            $api->get('/{uuid}', 'Specialtactics\L5Api\Tests\App\Http\Controllers\UserController@get');
            $api->post('/', 'Specialtactics\L5Api\Tests\App\Http\Controllers\UserController@post');
            $api->put('/{uuid}', 'Specialtactics\L5Api\Tests\App\Http\Controllers\UserController@put');
            $api->patch('/{uuid}', 'Specialtactics\L5Api\Tests\App\Http\Controllers\UserController@patch');
            $api->delete('/{uuid}', 'Specialtactics\L5Api\Tests\App\Http\Controllers\UserController@delete');
        });

        /**
         * Roles
         */
        $api->group(['prefix' => 'roles'], function ($api) {
            $api->get('/', 'Specialtactics\L5Api\Tests\App\Http\Controllers\RoleController@getAll');
        });

        /*
         * Forums
         */
        $api->group(['prefix' => 'forums'], function ($api) {
            $api->get('/', 'Specialtactics\L5Api\Tests\App\Http\Controllers\ForumController@getAll');
            $api->get('/{uuid}', 'Specialtactics\L5Api\Tests\App\Http\Controllers\ForumController@get');
            $api->post('/', 'Specialtactics\L5Api\Tests\App\Http\Controllers\ForumController@post');
            $api->patch('/{uuid}', 'Specialtactics\L5Api\Tests\App\Http\Controllers\ForumController@patch');
            $api->delete('/{uuid}', 'Specialtactics\L5Api\Tests\App\Http\Controllers\ForumController@delete');

            /*
             * Topics
             */
            $api->group(['prefix' => '/{forumUuid}/topics'], function ($api) {
                $api->get('/', 'Specialtactics\L5Api\Tests\App\Http\Controllers\TopicController@getAll');
                $api->get('/{uuid}', 'Specialtactics\L5Api\Tests\App\Http\Controllers\TopicController@get');
                $api->post('/', 'Specialtactics\L5Api\Tests\App\Http\Controllers\TopicController@post');
                $api->patch('/{uuid}', 'Specialtactics\L5Api\Tests\App\Http\Controllers\TopicController@patch');
                $api->delete('/{uuid}', 'Specialtactics\L5Api\Tests\App\Http\Controllers\TopicController@delete');
            });
        });

        /*
         * Topics
         */
        $api->group(['prefix' => 'topics'], function ($api) {
            /*
             * Posts
             */
            $api->group(['prefix' => '/{topicUuid}/posts'], function ($api) {
                $api->get('/', 'Specialtactics\L5Api\Tests\App\Http\Controllers\PostController@getAll');
                $api->get('/{uuid}', 'Specialtactics\L5Api\Tests\App\Http\Controllers\PostController@get');
                $api->post('/', 'Specialtactics\L5Api\Tests\App\Http\Controllers\PostController@post');
                $api->patch('/{uuid}', 'Specialtactics\L5Api\Tests\App\Http\Controllers\PostController@patch');
                $api->delete('/{uuid}', 'Specialtactics\L5Api\Tests\App\Http\Controllers\PostController@delete');
            });
        });
    });
});
