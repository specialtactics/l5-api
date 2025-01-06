<?php

namespace Specialtactics\L5Api\Tests\Unit;

use App\Models\Dates\ModelWithDates;
use Specialtactics\L5Api\APIBoilerplate;
use Specialtactics\L5Api\Tests\AppTestCase;
use Specialtactics\L5Api\Transformers\RestfulTransformer;

class DateTransformationTest extends AppTestCase
{
    /**
    * Check various dates are formatted correctly
     *
     * @test
     */
    public function canTransformDates()
    {
        $model = new ModelWithDates(['title' => 'Example dates', 'processed_at' => now(), 'scheduled_at' => now()->addDays(10), 'counted_at' => now()->subDays(5)]);
        $model->save();

        $transformed = (new RestfulTransformer)->transform($model);

        dump($transformed); // For github ci
        dump(APIBoilerplate::getResponseCaseType());

        $this->assertTrue($this->doesMatchFormat($transformed['processedAt']));
        $this->assertTrue($this->doesMatchFormat($transformed['scheduledAt']));
        $this->assertTrue($this->doesMatchFormat($transformed['countedAt']));
        $this->assertTrue($this->doesMatchFormat($transformed['createdAt']));
        $this->assertTrue($this->doesMatchFormat($transformed['updatedAt']));


        $this->assertEquals('Example dates', $transformed['title']);
    }

    protected function doesMatchFormat(string $date): bool
    {
        return preg_match('/\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}.\d{6}Z/', $date) === 1;
    }
}
