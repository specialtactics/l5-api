<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Specialtactics\L5Api\Http\Controllers\RestfulChildController as BaseController;

class ChildController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
