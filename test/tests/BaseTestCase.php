<?php

namespace Specialtactics\L5Api\Tests;

use Specialtactics\L5Api\APIBoilerplate;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

/**
 * Class BaseTestCase
 * @package Specialtactics\L5Api\Tests
 */
class BaseTestCase extends OrchestraTestCase
{
    /**
     * Set the API key-case on the API boilerplate class
     *
     * @param $case
     * @throws \ReflectionException
     */
    public function setAPIKeyCase($case)
    {
        $reflection = new \ReflectionProperty(APIBoilerplate::class, 'requestedKeyCaseFormat');
        $reflection->setAccessible(true);
        $reflection->setValue(null, $case);
    }
}
