<?php

/*
 * This file is part of Underscore.php
 *
 * (c) Maxime Fabre <ehtnam6@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Underscore;

use BadMethodCallException;
use Underscore\Methods\ArraysMethods;
use Underscore\Types\Arrays;
use Underscore\Types\Strings;

/**
 * Class MethodTest
 *
 * @package Underscore
 */
class MethodTest extends UnderscoreTestCase
{

    public function testThrowsErrorIfIncorrectMethod() : void
    {
        $this->expectException('BadMethodCallException');

        Underscore::invalid('foo');
    }

    public function testHasAccessToOriginalPhpFunctions() : void
    {
        $array = Arrays::from($this->array);
        $array = $array->intersect(['foo' => 'bar', 'kal' => 'mon']);

        $this->assertEquals(['foo' => 'bar'], $array->obtain());

        $string = Strings::repeat('foo', 2);
        $this->assertEquals('foofoo', $string);

        $string = Strings::from('   foo  ')->trim();
        $this->assertEquals('foo', $string->obtain());
    }

    public function testCantChainCertainMethods() : void
    {
        $method = Method::isUnchainable('Arrays', 'range');

        $this->assertTrue($method);
    }

    public function testCanGetMethodsFromType() : void
    {
        $method = Method::getMethodsFromType(Arrays::class);

        $this->assertEquals(ArraysMethods::class, $method);
    }

    public function testCanGetAliasesOfFunctions() : void
    {
        $method = Method::getAliasOf('select');

        $this->assertEquals('filter', $method);
    }

    public function testCanFindMethodsInClasses() : void
    {
        $method = Method::findInClasses(Underscore::class, 'range');

        /** @noinspection ClassConstantCanBeUsedInspection */
        $this->assertEquals('\Underscore\Types\Arrays', $method);
    }

    public function testCanThrowExceptionAtUnknownMethods() : void
    {
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        $this->expectException(BadMethodCallException::class,
            'The method Underscore\Types\Arrays::fuck does not exist');

        $test = Arrays::fuck($this);
    }
}
