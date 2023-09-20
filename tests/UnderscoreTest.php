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

namespace Underscore;

use Underscore\Dummies\DummyClass;
use Underscore\Dummies\DummyDefault;
use Underscore\Types\Arrays;
use Underscore\Types\Strings;

/**
 * Class UnderscoreTest
 *
 * @package Underscore
 */
class UnderscoreTest extends UnderscoreTestCase
{
    // Tests --------------------------------------------------------- /

    public function testCanWrapObject() : void
    {
        $under1 = new Underscore($this->array);
        $under2 = Underscore::from($this->array);

        $this->assertInstanceOf(Underscore::class, $under1);
        $this->assertInstanceOf(Arrays::class, $under2);
    }

    public function testCanRedirectToCorrectClass() : void
    {
        $under = Underscore::contains([1, 2, 3], 3);

        $this->assertTrue($under);
    }

    public function testCanSwitchTypesMidCourse() : void
    {
        $stringToArray = Strings::from('FOO.BAR')->lower()->explode('.')->last()->title();

        $this->assertSame('Bar', $stringToArray->obtain());
    }

    public function testCanWrapWithShortcutFunction() : void
    {
        // Skip if base function not present
        if ( ! \function_exists('underscore')) {
            $this->assertTrue(true);

            return;
        }

        $under = underscore($this->array);

        $this->assertInstanceOf(Underscore::class, $under);
    }

    public function testCanHaveAliasesForMethods() : void
    {
        $under = Arrays::select($this->arrayNumbers, fn($value) : bool => $value === 1);

        $this->assertSame([1], $under);
    }

    public function testUserCanExtendWithCustomFunctions() : void
    {
        Arrays::extend('fooify', fn($array) : string => 'bar');
        $this->assertSame('bar', Arrays::fooify(['foo']));

        Strings::extend('unfooer', fn($string) => Strings::replace($string, 'foo', 'bar'));
        $this->assertSame('bar', Strings::unfooer('foo'));
    }

    public function testBreakersCantAlterTheOriginalValue() : void
    {
        $object = Arrays::from([1, 2, 3]);
        $sum    = $object->sum();

        $this->assertSame(6, $sum);
        $this->assertSame([1, 2, 3], $object->obtain());
    }

    public function testClassesCanExtendCoreTypes() : void
    {
        $class = new DummyClass();
        $class->set('foo', 'bar');

        $this->assertSame('foobar', DummyDefault::create()->obtain());
        $this->assertSame('{"foo":"bar"}', $class->toJSON());
    }

    public function testClassesCanUpdateSubject() : void
    {
        $class  = new DummyClass();
        $class  = $class->getUsers()->toJSON();

        $class2 = DummyClass::create()->getUsers()->toJSON();

        $this->assertSame('[{"foo":"bar"},{"bar":"foo"}]', $class);
        $this->assertSame($class, $class2);
    }

    public function testClassesCanOverwriteUnderscore() : void
    {
        $class = new DummyClass();
        $class = $class->map(3)->paddingLeft(3)->toJSON();

        $this->assertSame('"009"', $class);
    }

    public function testMacrosCantConflictBetweenTypes() : void
    {
        Strings::extend('foobar', fn() : string => 'string');
        Arrays::extend('foobar', fn() : string => 'arrays');

        $this->assertSame('string', Strings::foobar());
        $this->assertSame('arrays', Arrays::foobar());
    }

    public function testCanCheckIfSubjectIsEmpty() : void
    {
        $array = Arrays::create();

        $this->assertTrue($array->isEmpty());
    }

    public function testCanParseToStringOnToString() : void
    {
        $array = Arrays::from($this->array);

        $this->assertSame('{"foo":"bar","bis":"ter"}', (string) $array);
    }

    public function testUnderscoreFindsRightClassToCall() : void
    {
        $numbers = [3, 4, 5];
        $product = Underscore::reduce($numbers, fn($w, $v) : int|float => $w * $v, 1);

        $this->assertSame(60, $product);
    }
}
