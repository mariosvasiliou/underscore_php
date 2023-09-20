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
 * Class StringTest
 *
 * @package Underscore\Types
 */
class StringTest extends UnderscoreTestCase
{

    public string $remove = 'foo foo bar foo kal ter son';

    public function provideAccord() : \Iterator
    {
        yield [10, '10 things'];
        yield [1, 'one thing'];
        yield [0, 'nothing'];
    }

    public function provideFind() : \Iterator
    {
        // Simple cases
        yield [false, 'foo', 'bar'];
        yield [true, 'foo', 'foo'];
        yield [true, 'FOO', 'foo', false];
        yield [false, 'FOO', 'foo', true];
        // Many needles, one haystack
        yield [true, ['foo', 'bar'], $this->remove];
        yield [false, ['vlu', 'bla'], $this->remove];
        yield [true, ['foo', 'vlu'], $this->remove, false, false];
        yield [false, ['foo', 'vlu'], $this->remove, false, true];
        // Many haystacks, one needle
        yield [true, 'foo', ['foo', 'bar']];
        yield [true, 'bar', ['foo', 'bar']];
        yield [false, 'foo', ['bar', 'kal']];
        yield [true, 'foo', ['foo', 'foo'], false, false];
        yield [false, 'foo', ['foo', 'bar'], false, true];
    }

    // Tests --------------------------------------------------------- /

    public function testCanCreateString() : void
    {
        $string = Strings::create();

        $this->assertSame('', $string->obtain());
    }

    public function testDoesntPluralizeTwice() : void
    {
        $string = new Strings('person');

        $this->assertSame('people', (string)$string->plural());
        $this->assertSame('people', (string)$string->plural());
    }

    public function testHasAccessToStrMethods() : void
    {
        $string1 = Strings::limit('foo', 1);
        $string2 = Underscore::from('foo')->limit(1)->obtain();

        $this->assertSame('f...', $string1);
        $this->assertSame('f...', $string2);
    }

    public function testCanRemoveTextFromString() : void
    {
        $return = Strings::remove($this->remove, 'bar');

        $this->assertSame('foo foo  foo kal ter son', $return);
    }

    public function testCanRemoveMultipleTextsFromString() : void
    {
        $return = Strings::remove($this->remove, ['foo', 'son']);

        $this->assertSame('bar  kal ter', $return);
    }

    public function testCanToggleBetweenTwoStrings() : void
    {
        $toggle = Strings::toggle('foo', 'foo', 'bar');
        $this->assertSame('bar', $toggle);
    }

    public function testCannotLooselyToggleBetweenStrings() : void
    {
        $toggle = Strings::toggle('dei', 'foo', 'bar');
        $this->assertSame('dei', $toggle);
    }

    public function testCanLooselyToggleBetweenStrings() : void
    {
        $toggle = Strings::toggle('dei', 'foo', 'bar', true);
        $this->assertSame('foo', $toggle);
    }

    public function testCanRepeatString() : void
    {
        $string = Strings::from('foo')->repeat(3)->obtain();

        $this->assertSame('foofoofoo', $string);
    }

    /**
     * @dataProvider provideFind
     *
     * @param      $expect
     * @param      $needle
     * @param      $haystack
     */
    public function testCanFindStringsInStrings(
        bool $expect,
        string|array $needle,
        $haystack,
        bool $caseSensitive = false,
        bool $absoluteFinding = false
    ) : void
    {
        $result = Strings::find($haystack, $needle, $caseSensitive, $absoluteFinding);

        $this->assertSame($expect, $result);
    }

    public function testCanAssertAStringStartsWith() : void
    {
        $this->assertTrue(Strings::startsWith('foobar', 'foo'));
        $this->assertFalse(Strings::startsWith('barfoo', 'foo'));
    }

    public function testCanAssertAStringEndsWith() : void
    {
        $this->assertTrue(Strings::endsWith('foobar', 'bar'));
        $this->assertFalse(Strings::endsWith('barfoo', 'bar'));
    }

    public function testStringsCanBeSlugged() : void
    {
        $this->assertSame('my-new-post', Strings::slugify('My_nEw\\\/  @ post!!!'));
        $this->assertSame('my_new_post', Strings::slugify('My nEw post!!!', '_'));
    }

    public function testRandomStringsCanBeGenerated() : void
    {
        $this->assertSame(40, \strlen(Strings::random(40)));
    }

    /**
     * @dataProvider provideAccord
     *
     * @param $number
     * @param $expect
     */
    public function testCanAccordAStringToItsNumeral(int $number, string $expect) : void
    {
        $result = Strings::accord($number, '%d things', 'one thing', 'nothing');

        $this->assertSame($expect, $result);
    }

    public function testCanSliceFromAString() : void
    {
        $string = Strings::sliceFrom('abcdef', 'c');

        $this->assertSame('cdef', $string);
    }

    public function testCanSliceToAString() : void
    {
        $string = Strings::sliceTo('abcdef', 'c');

        $this->assertSame('ab', $string);
    }

    public function testCanSliceAString() : void
    {
        $string = Strings::slice('abcdef', 'c');

        $this->assertSame(['ab', 'cdef'], $string);
    }

    public function testCanUseCorrectOrderForStrReplace() : void
    {
        $string = Strings::replace('foo', 'foo', 'bar');

        $this->assertSame('bar', $string);
    }

    public function testCanExplodeString() : void
    {
        $string = Strings::explode('foo bar foo', ' ');
        $this->assertSame(['foo', 'bar', 'foo'], $string);

        $string = Strings::explode('foo bar foo', ' ', -1);
        $this->assertSame(['foo', 'bar'], $string);
    }

    public function testCanGenerateRandomWords() : void
    {
        $string = Strings::randomStrings($words = 5, $size = 5);

        $result = ($words * $size) + ($words) - 1;
        $this->assertSame($result, \strlen($string));
    }

    public function testCanConvertToSnakeCase() : void
    {
        $string = Strings::toSnakeCase('thisIsAString');

        $this->assertSame('this_is_a_string', $string);
    }

    public function testCanConvertToCamelCase() : void
    {
        $string = Strings::toCamelCase('this_is_a_string');

        $this->assertSame('thisIsAString', $string);
    }

    public function testCanConvertToPascalCase() : void
    {
        $string = Strings::toPascalCase('this_is_a_string');

        $this->assertSame('ThisIsAString', $string);
    }

    public function testCanConvertToLowercase() : void
    {
        $this->assertSame('taylor', Strings::lower('TAYLOR'));
        $this->assertSame('άχιστη', Strings::lower('ΆΧΙΣΤΗ'));
    }

    public function testCanConvertToUppercase() : void
    {
        $this->assertSame('TAYLOR', Strings::upper('taylor'));
        $this->assertSame('ΆΧΙΣΤΗ', Strings::upper('άχιστη'));
    }

    public function testCanConvertToTitleCase() : void
    {
        $this->assertSame('Taylor', Strings::title('taylor'));
        $this->assertSame('Άχιστη', Strings::title('άχιστη'));
    }

    public function testCanLimitStringsByCharacters() : void
    {
        $this->assertSame('Tay...', Strings::limit('Taylor', 3));
        $this->assertSame('Taylor', Strings::limit('Taylor', 6));
        $this->assertSame('Tay___', Strings::limit('Taylor', 3, '___'));
    }

    public function testCanLimitByWords() : void
    {
        $this->assertSame('Taylor...', Strings::words('Taylor Otwell', 1));
        $this->assertSame('Taylor___', Strings::words('Taylor Otwell', 1, '___'));
        $this->assertSame('Taylor Otwell', Strings::words('Taylor Otwell', 3));
    }

    public function testCanCheckIfIsIp() : void
    {
        $this->assertTrue(Strings::isIp('192.168.1.1'));
        $this->assertFalse(Strings::isIp('foobar'));
    }

    public function testCanCheckIfIsEmail() : void
    {
        $this->assertTrue(Strings::isEmail('foo@bar.com'));
        $this->assertFalse(Strings::isEmail('foobar'));
    }

    public function testCanCheckIfIsUrl() : void
    {
        $this->assertTrue(Strings::isUrl('http://www.foo.com/'));
        $this->assertFalse(Strings::isUrl('foobar'));
    }

    public function testCanPrependString() : void
    {
        $this->assertSame('foobar', Strings::prepend('bar', 'foo'));
    }

    public function testCanAppendString() : void
    {
        $this->assertSame('foobar', Strings::append('foo', 'bar'));
    }

    public function testCanGetBaseClass() : void
    {
        $this->assertSame('Baz', Strings::baseClass(Baz::class));
    }
}
