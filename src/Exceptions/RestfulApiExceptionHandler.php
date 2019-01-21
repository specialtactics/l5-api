<?php

namespace Specialtactics\L5Api\Exceptions;

use Config;
use Exception;
use Dingo\Api\Exception\Handler as ExceptionHandler;
use Specialtactics\L5Api\APIBoilerplate;

/**
 * This class extends the Dingo API Exception Handler, and can be used to modify it's functionality, if required
 *
 * Class ApiHandler
 * @package App\Exceptions
 */
class RestfulApiExceptionHandler extends ExceptionHandler
{
    /**
     * Override prepare replacements function to add extra functionality
     *
     * @param Exception $exception
     * @return array
     */
    protected function prepareReplacements(Exception $exception)
    {
        // Run parent
        $replacements =  parent::prepareReplacements($exception);

        // Format error message field keys
        if ($exception instanceof \Illuminate\Validation\ValidationException) {
            $replacements = $this->formatCaseOfValidationMessages($replacements);
        }

        // Format response object keys
        $updatedFormat = [];
        foreach ($this->format as $key => $value) {
            $updatedFormat[APIBoilerplate::formatKeyCaseAccordingToReponseFormat($key)] = $value;
        }
        $this->format = $updatedFormat;

        return $replacements;
    }

    /**
     * Formats the case of validation message keys, if response case is not snake-case
     *
     * @param array $replacements
     * @return array
     */
    protected function formatCaseOfValidationMessages($replacements)
    {
        $errorKey = Config('api.errorFormat.errors');
        if (array_key_exists($errorKey, $replacements)) {
            $errorMessages = $replacements[$errorKey];

            if (Config(APIBoilerplate::CASE_TYPE_CONFIG_PATH) == APIBoilerplate::CAMEL_CASE) {
                $errorMessages = camel_case_array_keys($errorMessages);
            } else {
                $errorMessages = snake_case_array_keys($errorMessages);
            }

            $replacements[$errorKey] = $errorMessages;
        }

        return $replacements;
    }
}