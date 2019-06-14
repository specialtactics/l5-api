<?php

namespace Specialtactics\L5Api\Http\Response\Format;

class Json extends \Dingo\Api\Http\Response\Format\Json
{
    /**
     * Format an array or instance implementing Arrayable.
     *
     * @param array|\Illuminate\Contracts\Support\Arrayable $content
     *
     * @return string
     */
    public function formatArray($content)
    {
        if (array_key_exists('meta', $content)) {
            $meta = $content['meta'];

            // @todo: Change case depending on setting

            $content['meta'] = $meta;
        }

        return parent::formatArray($content);
    }
}
