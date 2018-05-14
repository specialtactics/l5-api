<?php

if (!function_exists('APIUser')) {
    function APIUser() {
        $user = app('Dingo\Api\Auth\Auth')->user();

        return $user;
    }
}

if (!function_exists('camel_case_array_keys')) {
    /**
     * Recursively camel-case an array's keys
     *
     * @param $array
     * @return array $array
     */
    function camel_case_array_keys($array) {
        foreach (array_keys($array) as $key) {
            // Get a reference to the value of the key (avoid copy)
            // Then remove that array element
            $value = &$array[$key];
            unset($array[$key]);

            // Transform key
            $transformedKey = camel_case($key);

            // Recurse
            if (is_array($value)) {
                $value = camel_case_array_keys($value);
            }

            // Store the transformed key with the referenced value
            $array[$transformedKey] = $value;

            // We'll be dealing with some large values, so memory cleanup is important
            unset($value);
        }

        return $array;
    }
}

if (!function_exists('snake_case_array_keys')) {
    /**
     * Recursively snake-case an array's keys
     *
     * @param $array
     * @return array $array
     */
    function snake_case_array_keys(array $array) {
        foreach (array_keys($array) as $key) {
            // Get a reference to the value of the key (avoid copy)
            // Then remove that array element
            $value = &$array[$key];
            unset($array[$key]);

            // Transform key
            $transformedKey = snake_case($key);

            // Recurse
            if (is_array($value)) {
                $value = snake_case_array_keys($value);
            }

            // Store the transformed key with the referenced value
            $array[$transformedKey] = $value;

            // We'll be dealing with some large values, so memory cleanup is important
            unset($value);
        }

        return $array;
    }
}

if (!function_exists('class_basename')) {

    /**
     * Get the basename of a class's FQNS name. This is proven to be the fastest way to do this (for now).
     *
     * @param string $className
     * @return string
     */
    function class_basename(string $className) {
        $reflection = new ReflectionClass($className);
        return $reflection->getShortName();
    }
}

if (!function_exists('get_calling_method')) {
    /**
     * Get the calling method name
     *
     * @return string
     */
    function get_calling_method() {
        return debug_backtrace()[1]['function'];
    }
}
