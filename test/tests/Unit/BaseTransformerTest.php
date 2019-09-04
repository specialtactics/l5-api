<?php

namespace Specialtactics\L5Api\Tests\Unit;

use Specialtactics\L5Api\Tests\AppTestCase;
use Specialtactics\L5Api\Tests\Fixtures\Models\ModelWithIdPK;
use Specialtactics\L5Api\Transformers\RestfulTransformer;

class BaseTransformerTest extends AppTestCase
{
    /**
     * Check that if a model has a primary key called "id", that it will be transformed correctly,
     * rather than being unset
     *
     * @test
     */
    public function canTransformModelWithPKOfId()
    {
        $model = new ModelWithIdPK(['example_attribute' => 'abc123']);

        $transformed = (new RestfulTransformer)->transform($model);

        $this->assertArrayHasKey('id', $transformed);
        $this->assertArrayHasKey('exampleAttribute', $transformed);
        $this->assertEquals($transformed['exampleAttribute'], 'abc123');
    }


    /**
     * @todo: Test for the situation of an attribute casted as array being null, and being converted to string inadvertently on transformation
     */

}
