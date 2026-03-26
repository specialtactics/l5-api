<?php

namespace Specialtactics\L5Api\Tests\A;

use Specialtactics\L5Api\Tests\BaseTestCase;
use PHPUnit\Framework\Attributes\Test;

class FirstTest extends BaseTestCase
{
    /**
     * @testdox Consumes testing bootstrap time
     */
    #[Test]
    public function testBootstrapTime()
    {
        $this->assertTrue(true);
    }
}
