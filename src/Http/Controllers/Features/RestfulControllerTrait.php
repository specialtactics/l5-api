<?php

namespace Specialtactics\L5Api\Http\Controllers\Features;

use App\Transformers\BaseTransformer;
use Illuminate\Http\Request;
use Specialtactics\L5Api\Helpers;

trait RestfulControllerTrait
{
    /**
     * Figure out which transformer to use
     *
     * Order of precedence:
     *  - Controller specified
     *  - Model specified
     *  - Base (extends RestfulTransformer)
     *
     * @return RestfulTransformer
     */
    protected function getTransformer()
    {
        $transformer = null;

        // Check if controller specifies a resource
        if (! is_null(static::$transformer)) {
            $transformer = static::$transformer;
        }

        // Otherwise, check if model is specified
        else {
            // If it is, check if the controller's model specifies the transformer
            if (! is_null(static::$model)) {
                // If it does, use it
                if (! is_null((static::$model)::$transformer)) {
                    $transformer = (static::$model)::$transformer;
                }
            }
        }

        // This is the default transformer, if one is not specified
        if (is_null($transformer)) {
            $transformer = BaseTransformer::class;
        }

        return new $transformer;
    }

    /**
     * Check if this request's body input is a collection of objects or not
     *
     * @return bool
     */
    protected function isRequestBodyACollection(Request $request)
    {
        $input = $request->input();

        // Check that the top-level of the body is an array
        if (is_array($input) && count($input) > 0) {
            // Check if the first element is an array (json object)
            $firstChild = array_shift($input);

            if (is_array($firstChild)) {
                return true;
            }
        }

        return false;
    }

    /**
     * This method determines whether the resource returned should undergo transformation or not.
     * The reason is, sometimes it is useful to return the untransformed resource (for example - for internal calls)
     *
     * Changed recently due to https://github.com/laravel/framework/pull/34884
     *
     * @return bool
     */
    protected function shouldTransform()
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);

        // We're going to check up to the top 20 calls (first 3-5 should be more than enough for almost any situation)
        $numberOfCallsToCheck = 20;
        $callsToCheck = [];

        for ($i = 0; $i < sizeof($trace) && $numberOfCallsToCheck > 0; ++$i) {
            // We only want function calls
            if (array_key_exists('file', $trace[$i])) {
                $callsToCheck[] = $trace[$i];
                --$numberOfCallsToCheck;
            }
        }

        // This should work for both the new and old scenarios
        if (basename($callsToCheck[1]['file']) == 'Controller.php' &&
            basename($callsToCheck[2]['file']) == 'ControllerDispatcher.php' &&
            $callsToCheck[2]['function'] == 'callAction') {
            return true;
        }

        return false;
    }

    /**
     * Prepend a Response message with a custom message
     * Useful for adding error info to internal request responses before returning them
     *
     * @param  \Dingo\Api\Http\Response  $response
     * @param  $message
     * @return \Dingo\Api\Http\Response
     */
    protected function prependResponseMessage($response, $message)
    {
        $content = $response->getOriginalContent();
        $content['message'] = $message . \Illuminate\Support\Arr::get($content, 'message', '');
        $response->setContent($content);

        return $response;
    }

    /**
     * Try to find the relation name of the child model in the parent model
     *
     * @param  $parent  Object Parent model instance
     * @param  $child  string Child model name
     * @return null|string
     */
    protected function getChildRelationNameForParent($parent, $child)
    {
        // Try model plural name
        $manyName = Helpers::modelRelationName($child, 'many');

        if (method_exists($parent, $manyName)) {
            return $manyName;
        }

        // Try model singular name
        $oneName = Helpers::modelRelationName($child, 'one');

        if (method_exists($parent, $oneName)) {
            return $oneName;
        }
    }
}
