<?php

namespace Specialtactics\L5Api\Tests\Unit;

use App\Models\BaseModel;
use Mockery;
use Specialtactics\L5Api\Tests\TestCase;
use Specialtactics\L5Api\Services\RestfulService;

class RestfulServiceTest extends TestCase
{
    /**
     * Test validation when updating a resource, whereby validation has multi-level rule keys
     *
     * @test
     */
    public function getRelevantValidationRulesConsidersMultiLevelRuleKeys()
    {
        $service = new RestfulService();

        // Example rules
        $mockedModel = Mockery::Mock(BaseModel::class)->makePartial();
        $mockedModel->shouldReceive('getValidationRulesUpdating')->andReturn([
            'unrelated_attribute' => 'required',
            'array_attribute' => 'required|array',
            'array_attribute.uuid_key' => 'required|uuid'
        ]);

        // Example request data
        $data = [
            'array_attribute' => [
                'uuid_key' => 'clearly not a uuid'
            ]
        ];

        $rules = $service->getRelevantValidationRules($mockedModel, $data);

        $this->assertArrayNotHasKey('unrelated_attribute', $rules);
        $this->assertArrayHasKey('array_attribute', $rules);
        $this->assertArrayHasKey('array_attribute.uuid_key', $rules);
    }
}
