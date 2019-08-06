<?php

namespace Specialtactics\L5Api\Tests\Unit\Helpers;

use Specialtactics\L5Api\Tests\AppTestCase;
use Specialtactics\L5Api\Helpers;

class KeyCaseTest extends AppTestCase
{
    /**
     * Keep in mind, there are always default limits on things
     */
    const MAX_DEPTH = 10;

    /**
     * An example nested array containing a multitude of cases (mostly snake)
     */
    const MIXED_CASES = [
        'snake_case' => [
            'something' => 5,
            'snake_something' => 6,
            'another_key' => [
                'easy' => true,
                'harder_one' => false,
            ],
        ],
        'conversion_rates' => [
            'AUD/USD' => 123.345,
            'GBP' => [
                'AUD' => 111,
            ],
        ],
        'alreadyCamelCase' => [
            'camelCase' => true,
            'but_also_snake_Case' => true,
            'more-nesting' => [
                'this-is-a-value',
                'another_value',
                'justAValue',
            ],
        ],
        'WhatAboutThis' => 'interesting',
        // Edge cases
        'why-would-anyone-do-this' => 'who knows, but probably someone will. Perhaps slugs?',
        'Another_weird_One' => true,
    ];

    /**
     * How the above array should look after the camel case filter
     */
    const AFTER_CAMEL = [
        'snakeCase' => [
            'something' => 5,
            'snakeSomething' => 6,
            'anotherKey' => [
                'easy' => true,
                'harderOne' => false,
            ],
        ],
        // Components we expect to stay the same
        'conversionRates' => [
            'AUD/USD' => 123.345,
            'GBP' => [
                'AUD' => 111,
            ],
        ],
        'alreadyCamelCase' => [
            'camelCase' => true,
            'butAlsoSnakeCase' => true,
            'moreNesting' => [
                'this-is-a-value',
                'another_value',
                'justAValue',
            ],
        ],
        'whatAboutThis' => 'interesting',
        // Edge cases
        'whyWouldAnyoneDoThis' => 'who knows, but probably someone will. Perhaps slugs?',
        'anotherWeirdOne' => true,
    ];

    /**
     * How the above array should look after the snake case filter
     */
    const AFTER_SNAKE = [
        'snake_case' => [
            'something' => 5,
            'snake_something' => 6,
            'another_key' => [
                'easy' => true,
                'harder_one' => false,
            ],
        ],
        // Components we expect to stay the same
        'conversion_rates' => [
            'AUD/USD' => 123.345,
            'GBP' => [
                'AUD' => 111,
            ],
        ],
        'already_camel_case' => [
            'camel_case' => true,
            'but_also_snake_case' => true,
            'more_nesting' => [
                'this-is-a-value',
                'another_value',
                'justAValue',
            ],
        ],
        'what_about_this' => 'interesting',
        // Edge cases
        'why_would_anyone_do_this' => 'who knows, but probably someone will. Perhaps slugs?',
        'another_weird_one' => true,
    ];

    /**
     * @test
     */
    public function camelCaseArrayKeys()
    {
        $result = Helpers::camelCaseArrayKeys(self::MIXED_CASES);
        $this->assertEquals($result, self::AFTER_CAMEL, 0.0, self::MAX_DEPTH);
    }

    /**
     * @test
     */
    public function snakeCaseArrayKeys()
    {
        $result = Helpers::snakeCaseArrayKeys(self::MIXED_CASES);
        $this->assertEquals($result, self::AFTER_SNAKE, 0.0, self::MAX_DEPTH);
    }

    /**
     * @test
     */
    public function keyCaseFiltersShouldProduceConsistentResults()
    {
        $result = Helpers::camelCaseArrayKeys(Helpers::snakeCaseArrayKeys(Helpers::camelCaseArrayKeys(Helpers::snakeCaseArrayKeys(self::MIXED_CASES))));
        $this->assertEquals($result, self::AFTER_CAMEL, 0.0, self::MAX_DEPTH);
    }

}
