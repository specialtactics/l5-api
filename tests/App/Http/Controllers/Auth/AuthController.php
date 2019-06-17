<?php

namespace Specialtactics\L5Api\Tests\App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Specialtactics\L5Api\Tests\App\Http\Controllers\Controller;
use Specialtactics\L5Api\Tests\App\Models\User;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Specialtactics\L5Api\Http\Controllers\Features\JWTAuthenticationTrait;

class AuthController extends Controller
{
    use JWTAuthenticationTrait;
}
