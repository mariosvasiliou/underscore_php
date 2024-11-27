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

use BadMethodCallException;
use PHPUnit\Framework\Attributes\Test;
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

    #[Test]
    public function throwsErrorIfIncorrectMethod(): void
    {
        $this->expectException('BadMethodCallException');

        Underscore::invalid('foo');
    }

    #[Test]
    public function hasAccessToOriginalPhpFunctions(): void
    {
        $array = Arrays::from($this->array);
        $array = $array->intersect(['foo' => 'bar', 'kal' => 'mon']);

        $this->assertSame(['foo' => 'bar'], $array->obtain());

        $string = Strings::repeat('foo', 2);
        $this->assertSame('foofoo', $string);

        $string = Strings::from('   foo  ')->trim();
        $this->assertSame('foo', $string->obtain());
    }

    #[Test]
    public function cantChainCertainMethods(): void
    {
        $method = Method::isUnchainable('Arrays', 'range');

        $this->assertTrue($method);
    }

    #[Test]
    public function canGetMethodsFromType(): void
    {
        $method = Method::getMethodsFromType(Arrays::class);

        $this->assertSame(ArraysMethods::class, $method);
    }

    #[Test]
    public function canGetAliasesOfFunctions(): void
    {
        $method = Method::getAliasOf('select');

        $this->assertSame('filter', $method);
    }

    #[Test]
    public function canFindMethodsInClasses(): void
    {
        $method = Method::findInClasses(Underscore::class, 'range');

        /** @noinspection ClassConstantCanBeUsedInspection */
        $this->assertEquals('\Underscore\Types\Arrays', $method);
    }

    #[Test]
    public function canThrowExceptionAtUnknownMethods(): void
    {
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        $this->expectException(BadMethodCallException::class);

        Arrays::fuck($this);
    }
}
