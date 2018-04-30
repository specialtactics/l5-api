<?php

if (!function_exists('APIUser')) {
    function APIUser() {
        $user = app('Dingo\Api\Auth\Auth')->user();

        return $user;
    }
}

if (!function_exists('camel_case_array')) {
    /**
     * Recursively camel-case an array
     * Uses pass by reference, and mutates the array in place
     *
     * @param $array
     */
    function camel_case_array(&$array) {
        foreach (array_keys($array) as $key) {
            // Get a reference to the value of the key (avoid copy)
            // Then remove that array element
            $value = &$array[$key];
            unset($array[$key]);

            // Transform key
            $transformedKey = camel_case($key);

            // Recurse
            if (is_array($value)) camel_case_array($value);

            // Store the transformed key with the referenced value
            $array[$transformedKey] = $value;

            // We'll be dealing with some large values, so memory cleanup is important
            unset($value);
        }
    }
}