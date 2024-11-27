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

use PHPUnit\Framework\Attributes\Test;
use Underscore\Underscore;
use Underscore\UnderscoreTestCase;

/**
 * Class ArraysTest
 *
 * @package Underscore\Types
 */
class ArraysTest extends UnderscoreTestCase
{
    // Tests --------------------------------------------------------- /

    #[Test]
    public function canCreateArray(): void
    {
        $array = Arrays::create();

        $this->assertSame([], $array->obtain());
    }

    #[Test]
    public function canUseClassDirectly(): void
    {
        $under = Arrays::get($this->array, 'foo');

        $this->assertSame('bar', $under);
    }

    #[Test]
    public function canCreateChainableObject(): void
    {
        $under = Underscore::from($this->arrayNumbers);
        $under = $under->get(1);

        $this->assertSame(2, $under);
    }

    #[Test]
    public function canGetKeys(): void
    {
        $array = Arrays::keys($this->array);

        $this->assertSame(['foo', 'bis'], $array);
    }

    #[Test]
    public function canGetValues(): void
    {
        $array = Arrays::values($this->array);

        $this->assertSame(['bar', 'ter'], $array);
    }

    #[Test]
    public function canSetValues(): void
    {
        $array = ['foo' => ['foo' => 'bar'], 'bar' => 'bis'];
        $array = Arrays::set($array, 'foo.bar.bis', 'ter');

        $this->assertSame('ter', $array['foo']['bar']['bis']);
        $this->assertArrayHasKey('bar', $array);
    }

    #[Test]
    public function canRemoveValues(): void
    {
        $array   = Arrays::remove($this->arrayMulti, '0.foo');
        $matcher = $this->arrayMulti;
        unset($matcher[0]['foo']);

        $this->assertSame($matcher, $array);
    }

    #[Test]
    public function canRemoveMultipleValues(): void
    {
        $array   = Arrays::remove($this->arrayMulti, ['0.foo', '1.foo']);
        $matcher = $this->arrayMulti;
        unset($matcher[0]['foo']);
        unset($matcher[1]['foo']);

        $this->assertSame($matcher, $array);
    }

    #[Test]
    public function canReturnAnArrayWithoutSomeValues(): void
    {
        $array = ['foo', 'foo', 'bar', 'bis', 'bar', 'bis', 'ter'];
        $array = Arrays::without($array, 'foo', 'bar');

        $this->assertSame([3 => 'bis', 5 => 'bis', 6 => 'ter'], $array);
        $this->assertNotContains('foo', Arrays::without($array, 'foo', 'bar'));
        $this->assertNotContains('bar', Arrays::without($array, 'foo', 'bar'));
        // new use case
        $exclusion = ['foo', 'bar'];
        $this->assertNotContains('foo', Arrays::without($array, $exclusion));
        $this->assertNotContains('bar', Arrays::without($array, $exclusion));
    }

    #[Test]
    public function canGetSumOfArray(): void
    {
        $array = Arrays::sum([1, 2, 3]);

        $this->assertSame(6, $array);
    }

    #[Test]
    public function canGetcountArray(): void
    {
        $array = Arrays::size([1, 2, 3]);

        $this->assertSame(3, $array);
    }

    #[Test]
    public function canSeeIfArrayContainsValue(): void
    {
        $true  = Arrays::contains([1, 2, 3], 2);
        $false = Arrays::contains([1, 2, 3], 5);

        $this->assertTrue($true);
        $this->assertFalse($false);
    }

    #[Test]
    public function canCheckIfHasValue(): void
    {
        $under = Arrays::has($this->array, 'foo');

        $this->assertTrue($under);
    }

    #[Test]
    public function canGetValueFromArray(): void
    {
        $array = ['foo' => ['bar' => 'bis']];
        $under = Arrays::get($array, 'foo.bar');

        $this->assertSame('bis', $under);
    }

    #[Test]
    public function cantConflictWithNativeFunctions(): void
    {
        $array = ['foo' => ['bar' => 'bis']];
        $under = Arrays::get($array, 'ter', 'str_replace');

        $this->assertSame('str_replace', $under);
    }

    #[Test]
    public function canFallbackClosure(): void
    {
        $array = ['foo' => ['bar' => 'bis']];
        $under = Arrays::get($array, 'ter', fn(): string => 'closure');

        $this->assertSame('closure', $under);
    }

    #[Test]
    public function canDoSomethingAtEachValue(): void
    {
        $closure = function(string $value, string $key): void {
            echo $key.':'.$value.':';
        };

        Arrays::at($this->array, $closure);
        $result = 'foo:bar:bis:ter:';

        $this->expectOutputString($result);
    }

    #[Test]
    public function canActOnEachValueFromArray(): void
    {
        $closure = fn($value, $key): string => $key.':'.$value;

        $under  = Arrays::each($this->array, $closure);
        $result = ['foo' => 'foo:bar', 'bis' => 'bis:ter'];

        $this->assertSame($result, $under);
    }

    #[Test]
    public function canFindAValueInAnArray(): void
    {
        $under = Arrays::find($this->arrayNumbers, fn($value): bool => $value % 2 === 0);
        $this->assertSame(2, $under);

        $unfound = Arrays::find($this->arrayNumbers, fn($value): bool => $value === 5);
        $this->assertNull($unfound);
    }

    #[Test]
    public function canFilterValuesFromAnArray(): void
    {
        $under = Arrays::filter($this->arrayNumbers, fn($value): bool => $value % 2 !== 0);

        $this->assertSame([0 => 1, 2 => 3], $under);
    }

    #[Test]
    public function canFilterRejectedValuesFromAnArray(): void
    {
        $under = Arrays::reject($this->arrayNumbers, fn($value): bool => $value % 2 !== 0);

        $this->assertSame([1 => 2], $under);
    }

    #[Test]
    public function canMatchAnArrayContent(): void
    {
        $under = Arrays::matches($this->arrayNumbers, fn($value): bool => \is_int($value));

        $this->assertTrue($under);
    }

    #[Test]
    public function canMatchPathOfAnArrayContent(): void
    {
        $under = Arrays::matchesAny($this->arrayNumbers, fn($value): bool => $value === 2);

        $this->assertTrue($under);
    }

    #[Test]
    public function canInvokeFunctionsOnValues(): void
    {
        $array = ['   foo  ', '   bar   '];
        $array = Arrays::invoke($array, 'trim');

        $this->assertSame(['foo', 'bar'], $array);
    }

    #[Test]
    public function canInvokeFunctionsOnValuesWithSingleArgument(): void
    {
        $array = ['_____foo', '____bar   '];
        $array = Arrays::invoke($array, 'trim', ' _');

        $this->assertSame(['foo', 'bar'], $array);
    }

    #[Test]
    public function canInvokeFunctionsWithDifferentArguments(): void
    {
        $array = ['_____foo  ', '__bar   '];
        $array = Arrays::invoke($array, 'trim', ['_', ' ']);

        $this->assertSame(['foo  ', '__bar'], $array);
    }

    #[Test]
    public function canPluckColumns(): void
    {
        $under   = Arrays::pluck($this->arrayMulti, 'foo');
        $matcher = ['bar', 'bar', null];

        $this->assertSame($matcher, $under);
    }

    #[Test]
    public function canCalculateAverageValue(): void
    {
        $average1 = [5, 10, 15, 20];
        $average2 = ['foo', 'b', 'ar'];
        $average3 = [['lol'], 10, 20];

        $average1 = Arrays::average($average1);
        $average2 = Arrays::average($average2);
        $average3 = Arrays::average($average3);

        $this->assertEqualsWithDelta(13.0, $average1, PHP_FLOAT_EPSILON);
        $this->assertEqualsWithDelta(0.0, $average2, PHP_FLOAT_EPSILON);
        $this->assertEqualsWithDelta(10.0, $average3, PHP_FLOAT_EPSILON);
    }

    #[Test]
    public function canGetFirstValue(): void
    {
        $under1 = Arrays::first($this->array);
        $under2 = Arrays::first($this->arrayNumbers, 2);

        $this->assertSame('bar', $under1);
        $this->assertSame([1, 2], $under2);
    }

    #[Test]
    public function canGetLastValue(): void
    {
        $under = Arrays::last($this->array);

        $this->assertSame('ter', $under);
    }

    #[Test]
    public function canGetLastElements(): void
    {
        $under = Arrays::last($this->arrayNumbers, 2);

        $this->assertSame([2, 3], $under);
    }

    #[Test]
    public function canXInitialElements(): void
    {
        $under = Arrays::initial($this->arrayNumbers);

        $this->assertSame([1, 2], $under);
    }

    #[Test]
    public function canGetRestFromArray(): void
    {
        $under = Arrays::rest($this->arrayNumbers);

        $this->assertSame([2, 3], $under);
    }

    #[Test]
    public function canCleanArray(): void
    {
        $array = [false, true, 0, 1, 'full', ''];
        $array = Arrays::clean($array);

        $this->assertSame([1 => true, 3 => 1, 4 => 'full'], $array);
    }

    #[Test]
    public function canGetMaxValueFromAnArray(): void
    {
        $under = Arrays::max($this->arrayNumbers);

        $this->assertSame(3, $under);
    }

    #[Test]
    public function canGetMaxValueFromAnArrayWithClosure(): void
    {
        $under = Arrays::max($this->arrayNumbers, fn($value): int|float => $value * -1);

        $this->assertSame(-1, $under);
    }

    #[Test]
    public function canGetMinValueFromAnArray(): void
    {
        $under = Arrays::min($this->arrayNumbers);

        $this->assertSame(1, $under);
    }

    #[Test]
    public function canGetMinValueFromAnArrayWithClosure(): void
    {
        $under = Arrays::min($this->arrayNumbers, fn($value): int|float => $value * -1);

        $this->assertSame(-3, $under);
    }

    #[Test]
    public function canSortKeys(): void
    {
        $under = Arrays::sortKeys(['z' => 0, 'b' => 1, 'r' => 2]);
        $this->assertSame(['b' => 1, 'r' => 2, 'z' => 0], $under);

        $under = Arrays::sortKeys(['z' => 0, 'b' => 1, 'r' => 2], 'desc');
        $this->assertSame(['z' => 0, 'r' => 2, 'b' => 1], $under);
    }

    #[Test]
    public function canSortValues(): void
    {
        $under = Arrays::sort([5, 3, 1, 2, 4], null, 'desc');
        $this->assertSame([5, 4, 3, 2, 1], $under);

        $under = Arrays::sort(range(1, 5), fn($value): bool => $value % 2 === 0);
        $this->assertSame([1, 3, 5, 2, 4], $under);
    }

    #[Test]
    public function canGroupValues(): void
    {
        $under = Arrays::group(range(1, 5), fn($value): bool => $value % 2 === 0);
        $matcher = [
            [1, 3, 5],
            [2, 4],
        ];

        $this->assertSame($matcher, $under);
    }

    #[Test]
    public function canGroupValuesWithSavingKeys(): void
    {
        $grouper = fn($value): bool => $value % 2 === 0;
        $under   = Arrays::group(range(1, 5), $grouper, true);
        $matcher = [
            [0 => 1, 2 => 3, 4 => 5],
            [1 => 2, 3 => 4],
        ];

        $this->assertSame($matcher, $under);
    }

    #[Test]
    public function canGroupValuesWithNonExistingKey(): void
    {
        $this->assertSame([], Arrays::group(range(1, 5), 'unknown', true));
        $this->assertSame([], Arrays::group(range(1, 5), 'unknown'));
    }

    #[Test]
    public function canCreateFromRange(): void
    {
        $range = Arrays::range(5);
        $this->assertSame([1, 2, 3, 4, 5], $range);

        $range = Arrays::range(-2, 2);
        $this->assertSame([-2, -1, 0, 1, 2], $range);

        $range = Arrays::range(1, 10, 2);
        $this->assertSame([1, 3, 5, 7, 9], $range);
    }

    #[Test]
    public function cantChainRange(): void
    {
        $this->expectException('Exception');

        Arrays::from($this->arrayNumbers)->range(5);
    }

    #[Test]
    public function canCreateFromRepeat(): void
    {
        $repeat = Arrays::repeat('foo', 3);

        $this->assertSame(['foo', 'foo', 'foo'], $repeat);
    }

    #[Test]
    public function canMergeArrays(): void
    {
        $array = Arrays::merge($this->array, ['foo' => 3], ['kal' => 'mon']);

        $this->assertSame(['foo' => 3, 'bis' => 'ter', 'kal' => 'mon'], $array);
    }

    #[Test]
    public function canGetRandomValue(): void
    {
        $array = Arrays::random($this->array);

        $this->assertContains($array, $this->array);
    }

    #[Test]
    public function canGetSeveralRandomValue(): void
    {
        $array = Arrays::random($this->arrayNumbers, 2);
        foreach ($array as $a) {
            $this->assertContains($a, $this->arrayNumbers);
        }
    }

    #[Test]
    public function canSearchForAValue(): void
    {
        $array = Arrays::search($this->array, 'ter');

        $this->assertSame('bis', $array);
    }

    #[Test]
    public function canDiffBetweenArrays(): void
    {
        $array = Arrays::diff($this->array, ['foo' => 'bar', 'ter' => 'kal']);
        $chain = Arrays::from($this->array)->diff(['foo' => 'bar', 'ter' => 'kal']);

        $this->assertSame(['bis' => 'ter'], $array);
        $this->assertSame(['bis' => 'ter'], $chain->obtain());
    }

    #[Test]
    public function canRemoveFirstValueFromAnArray(): void
    {
        $array = Arrays::removeFirst($this->array);

        $this->assertSame(['bis' => 'ter'], $array);
    }

    #[Test]
    public function canRemoveLasttValueFromAnArray(): void
    {
        $array = Arrays::removeLast($this->array);

        $this->assertSame(['foo' => 'bar'], $array);
    }

    #[Test]
    public function canImplodeAnArray(): void
    {
        $array = Arrays::implode($this->array, ',');

        $this->assertSame('bar,ter', $array);
    }

    #[Test]
    public function canFlattenArraysToDotNotation(): void
    {
        $array     = [
            'foo' => 'bar',
            'kal' => [
                'foo' => [
                    'bar',
                    'ter',
                ],
            ],
        ];
        $flattened = [
            'foo' => 'bar',
            'kal.foo.0' => 'bar',
            'kal.foo.1' => 'ter',
        ];

        $flatten = Arrays::flatten($array);

        $this->assertSame($flatten, $flattened);
    }

    #[Test]
    public function canFlattenArraysToCustomNotation(): void
    {
        $array     = [
            'foo' => 'bar',
            'kal' => [
                'foo' => [
                    'bar',
                    'ter',
                ],
            ],
        ];
        $flattened = [
            'foo' => 'bar',
            'kal/foo/0' => 'bar',
            'kal/foo/1' => 'ter',
        ];

        $flatten = Arrays::flatten($array, '/');

        $this->assertSame($flatten, $flattened);
    }

    #[Test]
    public function canReplaceValues(): void
    {
        $array   = Arrays::replace($this->array, 'foo', 'notfoo', 'notbar');
        $matcher = ['bis' => 'ter', 'notfoo' => 'notbar'];

        $this->assertSame($matcher, $array);
    }

    #[Test]
    public function canPrependValuesToArrays(): void
    {
        $array   = Arrays::prepend($this->array, 'foo');
        $matcher = [0 => 'foo', 'foo' => 'bar', 'bis' => 'ter'];

        $this->assertSame($matcher, $array);
    }

    #[Test]
    public function canAppendValuesToArrays(): void
    {
        $array   = Arrays::append($this->array, 'foo');
        $matcher = ['foo' => 'bar', 'bis' => 'ter', 0 => 'foo'];

        $this->assertSame($matcher, $array);
    }

    #[Test]
    public function canReplaceValuesInArrays(): void
    {
        $array = $this->array;
        $array = Arrays::replaceValue($array, 'bar', 'replaced');

        $this->assertSame('replaced', $array['foo']);
    }

    #[Test]
    public function canReplaceKeysInArray(): void
    {
        $array = $this->array;
        $array = Arrays::replaceKeys($array, ['bar', 'ter']);

        $this->assertSame(['bar' => 'bar', 'ter' => 'ter'], $array);
    }

    #[Test]
    public function canGetIntersectionOfTwoArrays(): void
    {
        $a     = ['foo', 'bar'];
        $b     = ['bar', 'baz'];
        $array = Arrays::intersection($a, $b);

        $this->assertSame(['bar'], $array);
    }

    #[Test]
    public function intersectsBooleanFlag(): void
    {
        $a = ['foo', 'bar'];
        $b = ['bar', 'baz'];

        $this->assertTrue(Arrays::intersects($a, $b));

        $a = ['bar'];
        $this->assertTrue(Arrays::intersects($a, $b));
        $a = ['foo'];
        $this->assertFalse(Arrays::intersects($a, $b));
    }

    #[Test]
    public function filterBy(): void
    {
        $a = [
            ['id' => 123, 'name' => 'foo', 'group' => 'primary', 'value' => 123456, 'when' => '2014-01-01'],
            ['id' => 456, 'name' => 'bar', 'group' => 'primary', 'value' => 1468, 'when' => '2014-07-15'],
            ['id' => 499, 'name' => 'baz', 'group' => 'secondary', 'value' => 2365, 'when' => '2014-08-23'],
            ['id' => 789, 'name' => 'ter', 'group' => 'primary', 'value' => 2468, 'when' => '2010-03-01'],
            ['id' => 888, 'name' => 'qux', 'value' => 6868, 'when' => '2015-01-01'],
            ['id' => 999, 'name' => 'flux', 'group' => null, 'value' => 6868, 'when' => '2015-01-01'],
        ];

        $b = Arrays::filterBy($a, 'name', 'baz');
        $this->assertCount(1, $b);
        $this->assertSame(2365, $b[0]['value']);

        $b = Arrays::filterBy($a, 'name', ['baz']);
        $this->assertCount(1, $b);
        $this->assertSame(2365, $b[0]['value']);

        $c = Arrays::filterBy($a, 'value', 2468);
        $this->assertCount(1, $c);
        $this->assertSame('primary', $c[0]['group']);

        $d = Arrays::filterBy($a, 'group', 'primary');
        $this->assertCount(3, $d);

        $e = Arrays::filterBy($a, 'value', 2000, 'lt');
        $this->assertCount(1, $e);
        $this->assertSame(1468, $e[0]['value']);

        $e = Arrays::filterBy($a, 'value', [2468, 2365], 'contains');
        $this->assertCount(2, $e);
        $this->assertContains(2468, Arrays::pluck($e, 'value'));
        $this->assertNotContains(1468, Arrays::pluck($e, 'value'));

        $e = Arrays::filterBy($a, 'when', '2014-02-01', 'older');
        $this->assertCount(2, $e);
        $this->assertContains('2014-01-01', Arrays::pluck($e, 'when'));
        $this->assertContains('2010-03-01', Arrays::pluck($e, 'when'));
        $this->assertNotContains('2014-08-23', Arrays::pluck($e, 'when'));

        $f = Arrays::filterBy($a, 'group', 'primary', 'ne');
        $this->assertCount(3, $f,
            "Count should pick up groups which are explicitly set as null AND those which don't have the property at all");
        $this->assertContains('qux', Arrays::pluck($f, 'name'));
        $this->assertContains('flux', Arrays::pluck($f, 'name'));
    }

    #[Test]
    public function findBy(): void
    {
        $a = [
            ['id' => 123, 'name' => 'foo', 'group' => 'primary', 'value' => 123456],
            ['id' => 456, 'name' => 'bar', 'group' => 'primary', 'value' => 1468],
            ['id' => 499, 'name' => 'baz', 'group' => 'secondary', 'value' => 2365],
            ['id' => 789, 'name' => 'ter', 'group' => 'primary', 'value' => 2468],
        ];

        $b = Arrays::findBy($a, 'name', 'baz');
        $this->assertIsArray($b);
        $this->assertCount(4, $b); // this is counting the number of keys in the array (id,name,group,value)
        $this->assertSame(2365, $b['value']);
        $this->assertArrayHasKey('name', $b);
        $this->assertArrayHasKey('group', $b);
        $this->assertArrayHasKey('value', $b);

        $c = Arrays::findBy($a, 'value', 2468);
        $this->assertIsArray($c);
        $this->assertCount(4, $c);
        $this->assertSame('primary', $c['group']);

        $d = Arrays::findBy($a, 'group', 'primary');
        $this->assertIsArray($d);
        $this->assertCount(4, $d);
        $this->assertSame('foo', $d['name']);

        $e = Arrays::findBy($a, 'value', 2000, 'lt');
        $this->assertIsArray($e);
        $this->assertCount(4, $e);
        $this->assertSame(1468, $e['value']);
    }

    #[Test]
    public function removeValue(): void
    {
        // numeric array
        $a = ['foo', 'bar', 'baz'];
        $this->assertCount(2, Arrays::removeValue($a, 'bar'));
        $this->assertNotContains('bar', Arrays::removeValue($a, 'bar'));
        $this->assertContains('foo', Arrays::removeValue($a, 'bar'));
        $this->assertContains('baz', Arrays::removeValue($a, 'bar'));
        // associative array
        $a = [
            'foo' => 'bar',
            'faz' => 'ter',
            'one' => 'two',
        ];
        $this->assertCount(2, Arrays::removeValue($a, 'bar'));
        $this->assertNotContains('bar', array_values(Arrays::removeValue($a, 'bar')));
        $this->assertContains('ter', array_values(Arrays::removeValue($a, 'bar')));
        $this->assertContains('two', array_values(Arrays::removeValue($a, 'bar')));
    }

    #[Test]
    public function canGetUniqueArray(): void
    {
        $a      = [1, 1, 2];
        $result = Arrays::unique($a);

        $this->assertSame([1, 2], $result);
    }

    #[Test]
    public function canIndexBy(): void
    {
        $array = [
            ['name' => 'moe', 'age' => 40],
            ['name' => 'larry', 'age' => 50],
            ['name' => 'curly', 'age' => 60],
        ];

        $expected = [
            40 => ['name' => 'moe', 'age' => 40],
            50 => ['name' => 'larry', 'age' => 50],
            60 => ['name' => 'curly', 'age' => 60],
        ];

        $this->assertSame($expected, Arrays::indexBy($array, 'age'));
    }

    #[Test]
    public function indexByReturnSome(): void
    {
        $array = [
            ['name' => 'moe', 'age' => 40],
            ['name' => 'larry', 'age' => 50],
            ['name' => 'curly'],
        ];

        $expected = [
            40 => ['name' => 'moe', 'age' => 40],
            50 => ['name' => 'larry', 'age' => 50],
        ];

        $this->assertSame($expected, Arrays::indexBy($array, 'age'));
    }

    #[Test]
    public function indexByReturnEmpty(): void
    {
        $array = [
            ['name' => 'moe', 'age' => 40],
            ['name' => 'larry', 'age' => 50],
            ['name' => 'curly'],
        ];

        $this->assertSame([], Arrays::indexBy($array, 'vaaaa'));
    }
}
