<?php

if (! function_exists('APIUser')) {
    function APIUser()
    {
        $user = app('Dingo\Api\Auth\Auth')->user();

        return $user;
    }
}

if (! function_exists('camel_case_array_keys')) {
    /**
     * Recursively camel-case an array's keys
     *
     * @deprecated Use Helpers.php
     *
     * @param  $array
     * @param  int|null  $levels  How many levels of an array keys to transform - by default recurse infinitely (null)
     * @return array $array
     */
    function camel_case_array_keys($array, $levels = null)
    {
        return Specialtactics\L5Api\Helpers::camelCaseArrayKeys($array, $levels);
    }
}

if (! function_exists('snake_case_array_keys')) {
    /**
     * Recursively snake-case an array's keys
     *
     * @deprecated Use Helpers.php
     *
     * @param  $array
     * @param  int|null  $levels  How many levels of an array keys to transform - by default recurse infinitely (null)
     * @return array $array
     */
    function snake_case_array_keys(array $array, $levels = null)
    {
        return Specialtactics\L5Api\Helpers::snakeCaseArrayKeys($array, $levels);
    }
}

if (! function_exists('model_relation_name')) {
    /**
     * Converts the name of a model class to the name of the relation of this resource on another model
     *
     * @deprecated Use Helpers.php
     *
     * @param  string  $resourceName  The name of the resource we are dealing with
     * @param  string  $relationType  The type of relation - ie.. one to.. X ('one', 'many')
     * @return string The name of the relation, as it would appear inside an eloquent model
     */
    function model_relation_name($resourceName, $relationType = 'many')
    {
        return Specialtactics\L5Api\Helpers::modelRelationName($resourceName, $relationType);
    }
}
