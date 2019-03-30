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
     * @param $array
     * @param int|null $levels How many levels of an array keys to transform - by default recurse infiniately (null)
     * @return array $array
     */
    function camel_case_array_keys($array, $levels = null)
    {
        foreach (array_keys($array) as $key) {
            // Get a reference to the value of the key (avoid copy)
            // Then remove that array element
            $value = &$array[$key];
            unset($array[$key]);

            // Transform key
            $transformedKey = camel_case($key);

            // Recurse
            if (is_array($value) && (is_null($levels) || --$levels > 0)) {
                $value = camel_case_array_keys($value, $levels);
            }

            // Store the transformed key with the referenced value
            $array[$transformedKey] = $value;

            // We'll be dealing with some large values, so memory cleanup is important
            unset($value);
        }

        return $array;
    }
}

if (! function_exists('snake_case_array_keys')) {
    /**
     * Recursively snake-case an array's keys
     *
     * @param $array
     * @param int|null $levels How many levels of an array keys to transform - by default recurse infiniately (null)
     * @return array $array
     */
    function snake_case_array_keys(array $array, $levels = null)
    {
        foreach (array_keys($array) as $key) {
            // Get a reference to the value of the key (avoid copy)
            // Then remove that array element
            $value = &$array[$key];
            unset($array[$key]);

            // Transform key
            $transformedKey = snake_case($key);

            // Recurse
            if (is_array($value) && (is_null($levels) || --$levels > 0)) {
                $value = snake_case_array_keys($value, $levels);
            }

            // Store the transformed key with the referenced value
            $array[$transformedKey] = $value;

            // We'll be dealing with some large values, so memory cleanup is important
            unset($value);
        }

        return $array;
    }
}

if (! function_exists('get_calling_method')) {
    /**
     * Get the calling method name
     *
     * @return string
     */
    function get_calling_method()
    {
        return debug_backtrace()[1]['function'];
    }
}

if (! function_exists('model_relation_name')) {
    /**
     * Converts the name of a model class to the name of the relation of this resource on another model
     *
     * @param string $relationType The type of relation - ie.. one to.. X ('one', 'many')
     * @return string The name of the relation, as it would appear inside an eloquent model
     * @throws \Exception
     */
    function model_relation_name($resourceName, $relationType = 'many')
    {
        if ($relationType == 'many') {
            return lcfirst(str_plural(class_basename($resourceName)));
        } elseif ($relationType == 'one') {
            return lcfirst(class_basename($resourceName));
        } else {
            throw new \Exception('Undefined relation type');
        }
    }
}
