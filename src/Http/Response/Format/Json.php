<?php

namespace Specialtactics\L5Api\Http\Response\Format;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;
use Specialtactics\L5Api\APIBoilerplate;

class Json extends \Dingo\Api\Http\Response\Format\Json
{
    /**
     * Format an array or instance implementing Arrayable.
     *
     * @param  array|\Illuminate\Contracts\Support\Arrayable  $content
     * @return string
     */
    public function formatArray($content)
    {
        if ($content instanceof Collection) {
            $content = $content->toArray();
        }

        if (array_key_exists('meta', $content) && is_array($content['meta'])) {
            // Change key-case of meta
            $content['meta'] = APIBoilerplate::formatKeyCaseAccordingToResponseFormat($content['meta']);
        }

        return parent::formatArray($content);
    }
}
