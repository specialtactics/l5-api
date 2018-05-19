<?php

namespace Specialtactics\L5Api\Http\Controllers\Features;

use App\Transformers\BaseTransformer;
use Illuminate\Http\Request;

trait RestfulControllerTrait
{
    /**
     * Figure out which transformer to use
     *
     * @return RestfulTransformer
     */
    protected function getTransformer()
    {
        $transformer = null;

        // Check if controller specifies a resource
        if ( ! is_null(static::$transformer)) {
            $transformer = static::$transformer;
        }

        // Otherwise, check if model is specified
        else {
            // If it is, check if the controller's model specifies the transformer
            if ( ! is_null(static::$model)) {
                // If it does, use it
                if ( ! is_null((static::$model)::$transformer)) {
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
        $input = $request->request->all();

        // Check that the top-level of the body is an array
        if (is_array($input) && sizeof($input) > 0) {

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
     * @return bool
     */
    protected function shouldTransform() {
        // If we are not called by this function, then we are not called by the router
        if (debug_backtrace()[2]['function'] != 'call_user_func_array') {
            return false;
        }

        return true;
    }

    /**
     * Prepend a Response message with a custom message
     * Useful for adding error info to internal request responses before returning them
     *
     * @param \Dingo\Api\Http\Response $response
     * @param $message
     * @return \Dingo\Api\Http\Response
     */
    protected function prependResponseMessage($response, $message) {
        $content = $response->getOriginalContent();
        $content['message'] = $message . $content['message'];
        $response->setContent($content);

        return $response;
    }
}
