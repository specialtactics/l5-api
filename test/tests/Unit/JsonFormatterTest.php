<?php

namespace Specialtactics\L5Api\Tests\Unit;

use App\Models\BaseModel;
use Mockery;
use Specialtactics\L5Api\APIBoilerplate;
use Specialtactics\L5Api\Tests\TestCase;
use Specialtactics\L5Api\Http\Response\Format\Json;

class JsonFormatterTest extends TestCase
{
    /**
     * Check that the JSON Formatter, formats the meta key case correctly
     *
     * @test
     */
    public function formatMetaArrayKeysAccordingToFormat()
    {
        $jsonFormatter = new Json;

        // Camel
        $this->setAPIKeyCase(APIBoilerplate::CAMEL_CASE);

        $content = [
            'do_not_change' => 'Unchanged',
            'meta' => [
                'needs_to_change' => true,
            ]
        ];

        $json = $jsonFormatter->formatArray($content);
        $formatted = json_decode($json, true);

        $this->assertArrayHasKey('do_not_change', $formatted);
        $this->assertArrayHasKey('needsToChange', $formatted['meta']);

        //
        // End to end
        //

        // Camel
        $this->setAPIKeyCase(APIBoilerplate::CAMEL_CASE);

        $response = $this->actingAsAdmin()->get('/forums');
        $responseData = $response->decodeResponseJson();

        $this->assertArrayHasKey('perPage', $responseData['meta']['pagination']);

        // Snake
        $this->setAPIKeyCase(APIBoilerplate::SNAKE_CASE);

        $response = $this->actingAsAdmin()->get('/forums');
        $responseData = $response->decodeResponseJson();

        $this->assertArrayHasKey('per_page', $responseData['meta']['pagination']);
    }
}
