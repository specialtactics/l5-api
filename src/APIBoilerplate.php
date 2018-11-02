<?php

namespace Specialtactics\L5Api;

use Config;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class APIBoilerplate {
    /**
     * Case type constants for configuring responses
     */
    const CAMEL_CASE = 'camel-case';
    const SNAKE_CASE = 'snake-case';

    /**
     * Case type config path
     */
    const CASE_TYPE_CONFIG_PATH = 'api.formatsOptions.caseType';

    /**
     * The header which can be used to override config provided case type
     */
    const CASE_TYPE_HEADER = 'X-Accept-Case-Type';

    /**
     * Get the required 'case type' for transforming response data
     *
     * @return string
     */
    public static function getResponseCaseType() {
        $format = null;

        // See if the client is requesting a specific case type
        $caseFormat = request()->header(static::CASE_TYPE_HEADER, null);
        if (!is_null($caseFormat)) {
            if ($caseFormat == static::CAMEL_CASE) {
                $format = static::CAMEL_CASE;
            } else if ($caseFormat == static::SNAKE_CASE) {
                $format = static::SNAKE_CASE;
            }
        }

        // Get case format from config (default case)
        if (is_null($format)) {
            $caseFormat = Config(static::CASE_TYPE_CONFIG_PATH);

            // Figure out required case
            if ($caseFormat == static::CAMEL_CASE || empty($caseFormat)) {
                $format = static::CAMEL_CASE;
            } else if ($caseFormat == static::SNAKE_CASE) {
                $format = static::SNAKE_CASE;
            } else {
                throw new HttpException(500, 'Invalid case type specified in API config.');
            }
        }

        return $format;
    }
}