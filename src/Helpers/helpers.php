<?php

if (!function_exists('APIUser')) {
    function APIUser() {
        $user = app('Dingo\Api\Auth\Auth')->user();

        return $user;
    }
}