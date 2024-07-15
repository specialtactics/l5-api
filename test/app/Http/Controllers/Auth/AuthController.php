<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Specialtactics\L5Api\Http\Controllers\Features\JWTAuthenticationTrait;

class AuthController extends Controller
{
    use JWTAuthenticationTrait;
}
