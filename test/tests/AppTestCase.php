<?php

namespace Specialtactics\L5Api\Tests;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use JWTAuth;
use Specialtactics\L5Api\APIBoilerplate;
use Database\Seeders\UserStorySeeder;

class AppTestCase extends SetupTestApp
{
    use DatabaseTransactions;

    /**
     * Set the currently logged in user for the application and Authorization headers for API request
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable
     * @param  string|null  $driver
     * @return $this
     */
    public function actingAs(UserContract $user, $driver = null)
    {
        parent::actingAs($user, $driver);

        return $this->withHeader('Authorization', 'Bearer ' . JWTAuth::fromUser($user));
    }

    /**
     * API Test case helper function for setting up
     * the request auth header as supplied user
     *
     * @param array $credentials
     * @return $this
     */
    public function actingAsUser($credentials)
    {
        if (!$token = JWTAuth::attempt($credentials)) {
            return $this;
        }

        $user = ($apiKey = Arr::get($credentials, 'api_key'))
            ? User::whereApiKey($apiKey)->firstOrFail()
            : User::whereEmail(Arr::get($credentials, 'email'))->firstOrFail();

        return $this->actingAs($user);
    }

    /**
     * API Test case helper function for setting up the request as a logged in admin user
     *
     * @return $this
     */
    public function actingAsAdmin()
    {
        $user = User::where('email', UserStorySeeder::ADMIN_CREDENTIALS[0])->firstOrFail();

        return $this->actingAs($user);
    }

    /**
     * Set the API key-case on the API boilerplate class
     *
     * @param $case
     * @throws \ReflectionException
     */
    public function setAPIKeyCase($case)
    {
        $reflection = new \ReflectionProperty(APIBoilerplate::class, 'requestedKeyCaseFormat');
        $reflection->setAccessible(true);
        $reflection->setValue(null, $case);
    }
}
