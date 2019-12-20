<?php

/*
 * This file is part of Underscore.php
 *
 * (c) Maxime Fabre <ehtnam6@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Underscore\Types;

use Underscore\UnderscoreTestCase;

/**
 * Class FunctionsTest
 *
 * @package Underscore\Types
 */
class FunctionsTest extends UnderscoreTestCase
{

    public function testCanCallFunctionOnlyOnce() : void
    {
        $number   = 0;
        $function = Functions::once(function() use (&$number) {
            ++$number;
        });

        $function();
        $function();

        $this->assertEquals(1, $number);
    }

    public function testCanCallFunctionOnlyXTimes() : void
    {
        $number   = 0;
        $function = Functions::only(function() use (&$number) {
            ++$number;
        }, 3);

        $function();
        $function();
        $function();
        $function();
        $function();

        $this->assertEquals(3, $number);
    }

    public function testCanCallFunctionAfterXTimes() : void
    {
        $number   = 0;
        $function = Functions::after(function() use (&$number) {
            ++$number;
        }, 3);

        $function();
        $function();
        $function();
        $function();
        $function();

        $this->assertEquals(2, $number);
    }

    public function testCanCacheFunctionResults() : void
    {
        $function = Functions::cache(function($string) {
            return microtime();
        });

        $result = $function('foobar');

        $this->assertEquals($result, $function('foobar'));
        $this->assertNotEquals($result, $function('barfoo'));
    }

    public function testCanThrottleFunctions() : void
    {
        $number   = 0;
        $function = Functions::throttle(function() use (&$number) {
            ++$number;
        }, 1);

        $function();
        $function();
        sleep(1);
        $function();

        $this->assertEquals(2, $number);
    }

    public function testCanPartiallyApplyArguments() : void
    {
        $function = Functions::partial(function() {
            return implode('', \func_get_args());
        }, 2, null, 6);

        $this->assertEquals('246', $function(4));
        $this->assertEquals('2468', $function(4, 8));
    }
}
