<?php

namespace Specialtactics\L5Api\Exceptions;

use Throwable;
use Dingo\Api\Exception\Handler as ExceptionHandler;
use Specialtactics\L5Api\APIBoilerplate;
use Specialtactics\L5Api\Helpers;

/**
 * This class extends the Dingo API Exception Handler, and can be used to modify it's functionality, if required
 *
 * Class ApiHandler
 */
class RestfulApiExceptionHandler extends ExceptionHandler
{
    /**
     * @var array Original replacements array
     */
    protected $originalReplacements = [];

    /**
     * Override prepare replacements function to add extra functionality
     *
     * @param Throwable $exception
     * @return array
     */
    protected function prepareReplacements(Throwable $exception)
    {
        // Run parent
        $replacements = parent::prepareReplacements($exception);

        // If the errors part is a MessageBag, turn it into an array so we can more easily handle it consistently
        $errorKey = Config('api.errorFormat.errors');
        if (array_key_exists($errorKey, $replacements) && ! is_array($replacements[$errorKey]) && is_object($replacements[$errorKey]) && $replacements[$errorKey] instanceof \Illuminate\Support\MessageBag) {
            $replacements[$errorKey] = $replacements[$errorKey]->toArray();
        }

        // Save original replacements, in case it's needed downstream
        $this->originalReplacements = $replacements;

        // Format error message field keys
        if ($exception instanceof \Illuminate\Validation\ValidationException || $exception instanceof \Dingo\Api\Exception\ResourceException) {
            $replacements = $this->formatCaseOfValidationMessages($replacements);
        }

        // Format response object keys
        $updatedFormat = [];
        foreach ($this->format as $key => $value) {
            $updatedFormat[APIBoilerplate::formatCaseAccordingToResponseFormat($key)] = $value;
        }
        $this->format = $updatedFormat;

        return $replacements;
    }

    /**
     * Get the original replacements array
     *
     * @return array
     */
    public function getOriginalReplacements()
    {
        return $this->originalReplacements;
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

            if (Config(APIBoilerplate::CASE_TYPE_CONFIG_PATH, APIBoilerplate::DEFAULT_CASE) == APIBoilerplate::CAMEL_CASE) {
                $errorMessages = Helpers::camelCaseArrayKeys($errorMessages);
            } else {
                $errorMessages = Helpers::snakeCaseArrayKeys($errorMessages);
            }

            $replacements[$errorKey] = $errorMessages;
        }

        return $replacements;
    }
}
