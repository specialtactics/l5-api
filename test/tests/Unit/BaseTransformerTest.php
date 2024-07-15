<?php

namespace Specialtactics\L5Api\Tests\Unit;

use Ramsey\Uuid\Uuid;
use Specialtactics\L5Api\Tests\AppTestCase;
use Specialtactics\L5Api\Tests\Fixtures\Models\ModelWithCasts;
use Specialtactics\L5Api\Tests\Fixtures\Models\ModelWithIdPK;
use Specialtactics\L5Api\Transformers\RestfulTransformer;

class BaseTransformerTest extends AppTestCase
{
    /**
     * Check that if a model has a primary key called "id", that it will be transformed correctly,
     * rather than being unset.
     *
     * @test
     */
    public function canTransformModelWithPKOfId()
    {
        $model = new ModelWithIdPK(['example_attribute' => 'abc123']);

        $transformed = (new RestfulTransformer())->transform($model);

        $this->assertArrayHasKey('id', $transformed);
        $this->assertArrayHasKey('exampleAttribute', $transformed);
        $this->assertEquals($transformed['exampleAttribute'], 'abc123');
    }

    /**
     * Make sure that an array cast attribute is transformed correctly, in situations when it's blank.
     *
     * @test
     */
    public function nullArrayCastWillBeEmptyArray()
    {
        //
        // Array attribute is null
        $model = new ModelWithCasts(['model_with_casts_id' => Uuid::uuid4()->toString(), 'array_attribute' => null]);

        $transformed = (new RestfulTransformer())->transform($model);

        $this->assertArrayHasKey('arrayAttribute', $transformed);
        $this->assertNull($transformed['arrayAttribute']);

        //
        // Array attribute is empty
        $model = new ModelWithCasts(['model_with_casts_id' => Uuid::uuid4()->toString(), 'array_attribute' => []]);

        $transformed = (new RestfulTransformer())->transform($model);

        $this->assertArrayHasKey('arrayAttribute', $transformed);
        $this->assertCount(0, $transformed['arrayAttribute']);
    }
}
