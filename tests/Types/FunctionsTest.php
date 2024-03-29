<?php
declare(strict_types=1);

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
        $function = Functions::once(function () use (&$number) : void {
            ++$number;
        });

        $function();
        $function();

        $this->assertSame(1, $number);
    }

    public function testCanCallFunctionOnlyXTimes() : void
    {
        $number   = 0;
        $function = Functions::only(function () use (&$number) : void {
            ++$number;
        }, 3);

        $function();
        $function();
        $function();
        $function();
        $function();

        $this->assertSame(3, $number);
    }

    public function testCanCallFunctionAfterXTimes() : void
    {
        $number   = 0;
        $function = Functions::after(function () use (&$number) : void {
            ++$number;
        }, 3);

        $function();
        $function();
        $function();
        $function();
        $function();

        $this->assertSame(2, $number);
    }

    public function testCanCacheFunctionResults() : void
    {
        $function = Functions::cache(fn($string) : string|float => microtime());

        $result = $function('foobar');

        $this->assertSame($result, $function('foobar'));
        $this->assertNotEquals($result, $function('barfoo'));
    }

    public function testCanThrottleFunctions() : void
    {
        $number   = 0;
        $function = Functions::throttle(function () use (&$number) : void {
            ++$number;
        }, 1);

        $function();
        $function();
        sleep(1);
        $function();

        $this->assertSame(2, $number);
    }

    public function testCanPartiallyApplyArguments() : void
    {
        $function = Functions::partial(fn() : string => implode('', \func_get_args()), 2, null, 6);

        $this->assertSame('246', $function(4));
        $this->assertSame('2468', $function(4, 8));
    }
}
