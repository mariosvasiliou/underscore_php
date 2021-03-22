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

    public function testCanCreateArray() : void
    {
        $array = Arrays::create();

        $this->assertSame([], $array->obtain());
    }

    public function testCanUseClassDirectly() : void
    {
        $under = Arrays::get($this->array, 'foo');

        $this->assertSame('bar', $under);
    }

    public function testCanCreateChainableObject() : void
    {
        $under = Underscore::from($this->arrayNumbers);
        $under = $under->get(1);

        $this->assertSame(2, $under);
    }

    public function testCanGetKeys() : void
    {
        $array = Arrays::keys($this->array);

        $this->assertSame(['foo', 'bis'], $array);
    }

    public function testCanGetValues() : void
    {
        $array = Arrays::values($this->array);

        $this->assertSame(['bar', 'ter'], $array);
    }

    public function testCanSetValues() : void
    {
        $array = ['foo' => ['foo' => 'bar'], 'bar' => 'bis'];
        $array = Arrays::set($array, 'foo.bar.bis', 'ter');

        $this->assertSame('ter', $array['foo']['bar']['bis']);
        $this->assertArrayHasKey('bar', $array);
    }

    public function testCanRemoveValues() : void
    {
        $array   = Arrays::remove($this->arrayMulti, '0.foo');
        $matcher = $this->arrayMulti;
        unset($matcher[0]['foo']);

        $this->assertSame($matcher, $array);
    }

    public function testCanRemoveMultipleValues() : void
    {
        $array   = Arrays::remove($this->arrayMulti, ['0.foo', '1.foo']);
        $matcher = $this->arrayMulti;
        unset($matcher[0]['foo']);
        unset($matcher[1]['foo']);

        $this->assertSame($matcher, $array);
    }

    public function testCanReturnAnArrayWithoutSomeValues() : void
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

    public function testCanGetSumOfArray() : void
    {
        $array = Arrays::sum([1, 2, 3]);

        $this->assertSame(6, $array);
    }

    public function testCanGetcountArray() : void
    {
        $array = Arrays::size([1, 2, 3]);

        $this->assertSame(3, $array);
    }

    public function testCanSeeIfArrayContainsValue() : void
    {
        $true  = Arrays::contains([1, 2, 3], 2);
        $false = Arrays::contains([1, 2, 3], 5);

        $this->assertTrue($true);
        $this->assertFalse($false);
    }

    public function testCanCheckIfHasValue() : void
    {
        $under = Arrays::has($this->array, 'foo');

        $this->assertTrue($under);
    }

    public function testCanGetValueFromArray() : void
    {
        $array = ['foo' => ['bar' => 'bis']];
        $under = Arrays::get($array, 'foo.bar');

        $this->assertSame('bis', $under);
    }

    public function testCantConflictWithNativeFunctions() : void
    {
        $array = ['foo' => ['bar' => 'bis']];
        $under = Arrays::get($array, 'ter', 'str_replace');

        $this->assertSame('str_replace', $under);
    }

    public function testCanFallbackClosure() : void
    {
        $array = ['foo' => ['bar' => 'bis']];
        $under = Arrays::get($array, 'ter', function() {
            return 'closure';
        });

        $this->assertSame('closure', $under);
    }

    public function testCanDoSomethingAtEachValue() : void
    {
        $closure = function($value, $key) {
            echo $key.':'.$value.':';
        };

        Arrays::at($this->array, $closure);
        $result = 'foo:bar:bis:ter:';

        $this->expectOutputString($result);
    }

    public function testCanActOnEachValueFromArray() : void
    {
        $closure = function($value, $key) {
            return $key.':'.$value;
        };

        $under  = Arrays::each($this->array, $closure);
        $result = ['foo' => 'foo:bar', 'bis' => 'bis:ter'];

        $this->assertSame($result, $under);
    }

    public function testCanFindAValueInAnArray() : void
    {
        $under = Arrays::find($this->arrayNumbers, function($value) {
            return $value % 2 === 0;
        });
        $this->assertSame(2, $under);

        $unfound = Arrays::find($this->arrayNumbers, function($value) {
            return $value === 5;
        });
        $this->assertNull($unfound);
    }

    public function testCanFilterValuesFromAnArray() : void
    {
        $under = Arrays::filter($this->arrayNumbers, function($value) {
            return $value % 2 !== 0;
        });

        $this->assertSame([0 => 1, 2 => 3], $under);
    }

    public function testCanFilterRejectedValuesFromAnArray() : void
    {
        $under = Arrays::reject($this->arrayNumbers, function($value) {
            return $value % 2 !== 0;
        });

        $this->assertSame([1 => 2], $under);
    }

    public function testCanMatchAnArrayContent() : void
    {
        $under = Arrays::matches($this->arrayNumbers, function($value) {
            return \is_int($value);
        });

        $this->assertTrue($under);
    }

    public function testCanMatchPathOfAnArrayContent() : void
    {
        $under = Arrays::matchesAny($this->arrayNumbers, function($value) {
            return $value === 2;
        });

        $this->assertTrue($under);
    }

    public function testCanInvokeFunctionsOnValues() : void
    {
        $array = ['   foo  ', '   bar   '];
        $array = Arrays::invoke($array, 'trim');

        $this->assertSame(['foo', 'bar'], $array);
    }

    public function testCanInvokeFunctionsOnValuesWithSingleArgument() : void
    {
        $array = ['_____foo', '____bar   '];
        $array = Arrays::invoke($array, 'trim', ' _');

        $this->assertSame(['foo', 'bar'], $array);
    }

    public function testCanInvokeFunctionsWithDifferentArguments() : void
    {
        $array = ['_____foo  ', '__bar   '];
        $array = Arrays::invoke($array, 'trim', ['_', ' ']);

        $this->assertSame(['foo  ', '__bar'], $array);
    }

    public function testCanPluckColumns() : void
    {
        $under   = Arrays::pluck($this->arrayMulti, 'foo');
        $matcher = ['bar', 'bar', null];

        $this->assertSame($matcher, $under);
    }

    public function testCanCalculateAverageValue() : void
    {
        $average1 = [5, 10, 15, 20];
        $average2 = ['foo', 'b', 'ar'];
        $average3 = [['lol'], 10, 20];

        $average1 = Arrays::average($average1);
        $average2 = Arrays::average($average2);
        $average3 = Arrays::average($average3);

        $this->assertSame(13.0, $average1);
        $this->assertSame(0.0, $average2);
        $this->assertSame(10.0, $average3);
    }

    public function testCanGetFirstValue() : void
    {
        $under1 = Arrays::first($this->array);
        $under2 = Arrays::first($this->arrayNumbers, 2);

        $this->assertSame('bar', $under1);
        $this->assertSame([1, 2], $under2);
    }

    public function testCanGetLastValue() : void
    {
        $under = Arrays::last($this->array);

        $this->assertSame('ter', $under);
    }

    public function testCanGetLastElements() : void
    {
        $under = Arrays::last($this->arrayNumbers, 2);

        $this->assertSame([2, 3], $under);
    }

    public function testCanXInitialElements() : void
    {
        $under = Arrays::initial($this->arrayNumbers);

        $this->assertSame([1, 2], $under);
    }

    public function testCanGetRestFromArray() : void
    {
        $under = Arrays::rest($this->arrayNumbers);

        $this->assertSame([2, 3], $under);
    }

    public function testCanCleanArray() : void
    {
        $array = [false, true, 0, 1, 'full', ''];
        $array = Arrays::clean($array);

        $this->assertSame([1 => true, 3 => 1, 4 => 'full'], $array);
    }

    public function testCanGetMaxValueFromAnArray() : void
    {
        $under = Arrays::max($this->arrayNumbers);

        $this->assertSame(3, $under);
    }

    public function testCanGetMaxValueFromAnArrayWithClosure() : void
    {
        $under = Arrays::max($this->arrayNumbers, function($value) {
            return $value * -1;
        });

        $this->assertSame(-1, $under);
    }

    public function testCanGetMinValueFromAnArray() : void
    {
        $under = Arrays::min($this->arrayNumbers);

        $this->assertSame(1, $under);
    }

    public function testCanGetMinValueFromAnArrayWithClosure() : void
    {
        $under = Arrays::min($this->arrayNumbers, function($value) {
            return $value * -1;
        });

        $this->assertSame(-3, $under);
    }

    public function testCanSortKeys() : void
    {
        $under = Arrays::sortKeys(['z' => 0, 'b' => 1, 'r' => 2]);
        $this->assertSame(['b' => 1, 'r' => 2, 'z' => 0], $under);

        $under = Arrays::sortKeys(['z' => 0, 'b' => 1, 'r' => 2], 'desc');
        $this->assertSame(['z' => 0, 'r' => 2, 'b' => 1], $under);
    }

    public function testCanSortValues() : void
    {
        $under = Arrays::sort([5, 3, 1, 2, 4], null, 'desc');
        $this->assertSame([5, 4, 3, 2, 1], $under);

        $under = Arrays::sort(range(1, 5), function($value) {
            return $value % 2 === 0;
        });
        $this->assertSame([1, 3, 5, 2, 4], $under);
    }

    public function testCanGroupValues() : void
    {
        $under   = Arrays::group(range(1, 5), function($value) {
            return $value % 2 === 0;
        });
        $matcher = [
            [1, 3, 5],
            [2, 4],
        ];

        $this->assertSame($matcher, $under);
    }

    public function testCanGroupValuesWithSavingKeys() : void
    {
        $grouper = function($value) {
            return $value % 2 === 0;
        };
        $under   = Arrays::group(range(1, 5), $grouper, true);
        $matcher = [
            [0 => 1, 2 => 3, 4 => 5],
            [1 => 2, 3 => 4],
        ];

        $this->assertSame($matcher, $under);
    }

    public function testCanGroupValuesWithNonExistingKey() : void
    {
        $this->assertSame([], Arrays::group(range(1, 5), 'unknown', true));
        $this->assertSame([], Arrays::group(range(1, 5), 'unknown'));
    }

    public function testCanCreateFromRange() : void
    {
        $range = Arrays::range(5);
        $this->assertSame([1, 2, 3, 4, 5], $range);

        $range = Arrays::range(-2, 2);
        $this->assertSame([-2, -1, 0, 1, 2], $range);

        $range = Arrays::range(1, 10, 2);
        $this->assertSame([1, 3, 5, 7, 9], $range);
    }

    public function testCantChainRange() : void
    {
        $this->expectException('Exception');

        Arrays::from($this->arrayNumbers)->range(5);
    }

    public function testCanCreateFromRepeat() : void
    {
        $repeat = Arrays::repeat('foo', 3);

        $this->assertSame(['foo', 'foo', 'foo'], $repeat);
    }

    public function testCanMergeArrays() : void
    {
        $array = Arrays::merge($this->array, ['foo' => 3], ['kal' => 'mon']);

        $this->assertSame(['foo' => 3, 'bis' => 'ter', 'kal' => 'mon'], $array);
    }

    public function testCanGetRandomValue() : void
    {
        $array = Arrays::random($this->array);

        $this->assertContains($array, $this->array);
    }

    public function testCanGetSeveralRandomValue() : void
    {
        $array = Arrays::random($this->arrayNumbers, 2);
        foreach ($array as $a) {
            $this->assertContains($a, $this->arrayNumbers);
        }
    }

    public function testCanSearchForAValue() : void
    {
        $array = Arrays::search($this->array, 'ter');

        $this->assertSame('bis', $array);
    }

    public function testCanDiffBetweenArrays() : void
    {
        $array = Arrays::diff($this->array, ['foo' => 'bar', 'ter' => 'kal']);
        $chain = Arrays::from($this->array)->diff(['foo' => 'bar', 'ter' => 'kal']);

        $this->assertSame(['bis' => 'ter'], $array);
        $this->assertSame(['bis' => 'ter'], $chain->obtain());
    }

    public function testCanRemoveFirstValueFromAnArray() : void
    {
        $array = Arrays::removeFirst($this->array);

        $this->assertSame(['bis' => 'ter'], $array);
    }

    public function testCanRemoveLasttValueFromAnArray() : void
    {
        $array = Arrays::removeLast($this->array);

        $this->assertSame(['foo' => 'bar'], $array);
    }

    public function testCanImplodeAnArray() : void
    {
        $array = Arrays::implode($this->array, ',');

        $this->assertSame('bar,ter', $array);
    }

    public function testCanFlattenArraysToDotNotation() : void
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

    public function testCanFlattenArraysToCustomNotation() : void
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

    public function testCanReplaceValues() : void
    {
        $array   = Arrays::replace($this->array, 'foo', 'notfoo', 'notbar');
        $matcher = ['bis' => 'ter', 'notfoo' => 'notbar'];

        $this->assertSame($matcher, $array);
    }

    public function testCanPrependValuesToArrays() : void
    {
        $array   = Arrays::prepend($this->array, 'foo');
        $matcher = [0 => 'foo', 'foo' => 'bar', 'bis' => 'ter'];

        $this->assertSame($matcher, $array);
    }

    public function testCanAppendValuesToArrays() : void
    {
        $array   = Arrays::append($this->array, 'foo');
        $matcher = ['foo' => 'bar', 'bis' => 'ter', 0 => 'foo'];

        $this->assertSame($matcher, $array);
    }

    public function testCanReplaceValuesInArrays() : void
    {
        $array = $this->array;
        $array = Arrays::replaceValue($array, 'bar', 'replaced');

        $this->assertSame('replaced', $array['foo']);
    }

    public function testCanReplaceKeysInArray() : void
    {
        $array = $this->array;
        $array = Arrays::replaceKeys($array, ['bar', 'ter']);

        $this->assertSame(['bar' => 'bar', 'ter' => 'ter'], $array);
    }

    public function testCanGetIntersectionOfTwoArrays() : void
    {
        $a     = ['foo', 'bar'];
        $b     = ['bar', 'baz'];
        $array = Arrays::intersection($a, $b);

        $this->assertSame(['bar'], $array);
    }

    public function testIntersectsBooleanFlag() : void
    {
        $a = ['foo', 'bar'];
        $b = ['bar', 'baz'];

        $this->assertTrue(Arrays::intersects($a, $b));

        $a = ['bar'];
        $this->assertTrue(Arrays::intersects($a, $b));
        $a = ['foo'];
        $this->assertFalse(Arrays::intersects($a, $b));
    }

    public function testFilterBy() : void
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
        $this->assertCount(3, $f, 'Count should pick up groups which are explicitly set as null AND those which don\'t have the property at all');
        $this->assertContains('qux', Arrays::pluck($f, 'name'));
        $this->assertContains('flux', Arrays::pluck($f, 'name'));
    }

    public function testFindBy() : void
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

    public function testRemoveValue() : void
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

    public function testCanGetUniqueArray() : void
    {
        $a      = [1, 1, 2];
        $result = Arrays::unique($a);

        $this->assertSame([1, 2], $result);
    }

    public function testCanIndexBy() : void
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

    public function testIndexByReturnSome() : void
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

    public function testIndexByReturnEmpty() : void
    {
        $array = [
            ['name' => 'moe', 'age' => 40],
            ['name' => 'larry', 'age' => 50],
            ['name' => 'curly'],
        ];

        $this->assertSame([], Arrays::indexBy($array, 'vaaaa'));
    }
}
