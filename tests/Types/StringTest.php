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

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
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

    public static function provideAccord(): \Iterator
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

    #[Test]
    public function canCreateString(): void
    {
        $string = Strings::create();

        $this->assertSame('', $string->obtain());
    }

    #[Test]
    public function doesntPluralizeTwice(): void
    {
        $string = new Strings('person');

        $this->assertSame('people', (string)$string->plural());
        $this->assertSame('people', (string)$string->plural());
    }

    #[Test]
    public function hasAccessToStrMethods(): void
    {
        $string1 = Strings::limit('foo', 1);
        $string2 = Underscore::from('foo')->limit(1)->obtain();

        $this->assertSame('f...', $string1);
        $this->assertSame('f...', $string2);
    }

    #[Test]
    public function canRemoveTextFromString(): void
    {
        $return = Strings::remove($this->remove, 'bar');

        $this->assertSame('foo foo  foo kal ter son', $return);
    }

    #[Test]
    public function canRemoveMultipleTextsFromString(): void
    {
        $return = Strings::remove($this->remove, ['foo', 'son']);

        $this->assertSame('bar  kal ter', $return);
    }

    #[Test]
    public function canToggleBetweenTwoStrings(): void
    {
        $toggle = Strings::toggle('foo', 'foo', 'bar');
        $this->assertSame('bar', $toggle);
    }

    #[Test]
    public function cannotLooselyToggleBetweenStrings(): void
    {
        $toggle = Strings::toggle('dei', 'foo', 'bar');
        $this->assertSame('dei', $toggle);
    }

    #[Test]
    public function canLooselyToggleBetweenStrings(): void
    {
        $toggle = Strings::toggle('dei', 'foo', 'bar', true);
        $this->assertSame('foo', $toggle);
    }

    #[Test]
    public function canRepeatString(): void
    {
        $string = Strings::from('foo')->repeat(3)->obtain();

        $this->assertSame('foofoofoo', $string);
    }

    /**
     *
     * @param      $expect
     * @param      $needle
     * @param      $haystack
     */
    #[DataProvider('provideFind')]
    #[Test]
    public function canFindStringsInStrings(
        bool $expect,
        string|array $needle,
        string|array $haystack,
        bool $caseSensitive = false,
        bool $absoluteFinding = false
    ) : void
    {
        $result = Strings::find($haystack, $needle, $caseSensitive, $absoluteFinding);

        $this->assertSame($expect, $result);
    }

    #[Test]
    public function canAssertAStringStartsWith(): void
    {
        $this->assertTrue(Strings::startsWith('foobar', 'foo'));
        $this->assertFalse(Strings::startsWith('barfoo', 'foo'));
    }

    #[Test]
    public function canAssertAStringEndsWith(): void
    {
        $this->assertTrue(Strings::endsWith('foobar', 'bar'));
        $this->assertFalse(Strings::endsWith('barfoo', 'bar'));
    }

    #[Test]
    public function stringsCanBeSlugged(): void
    {
        $this->assertSame('my-new-post', Strings::slugify('My_nEw\\\/  @ post!!!'));
        $this->assertSame('my_new_post', Strings::slugify('My nEw post!!!', '_'));
    }

    #[Test]
    public function randomStringsCanBeGenerated(): void
    {
        $this->assertSame(40, \strlen(Strings::random(40)));
    }

    /**
     *
     * @param $number
     * @param $expect
     */
    #[DataProvider('provideAccord')]
    #[Test]
    public function canAccordAStringToItsNumeral(int $number, string $expect): void
    {
        $result = Strings::accord($number, '%d things', 'one thing', 'nothing');

        $this->assertSame($expect, $result);
    }

    #[Test]
    public function canSliceFromAString(): void
    {
        $string = Strings::sliceFrom('abcdef', 'c');

        $this->assertSame('cdef', $string);
    }

    #[Test]
    public function canSliceToAString(): void
    {
        $string = Strings::sliceTo('abcdef', 'c');

        $this->assertSame('ab', $string);
    }

    #[Test]
    public function canSliceAString(): void
    {
        $string = Strings::slice('abcdef', 'c');

        $this->assertSame(['ab', 'cdef'], $string);
    }

    #[Test]
    public function canUseCorrectOrderForStrReplace(): void
    {
        $string = Strings::replace('foo', 'foo', 'bar');

        $this->assertSame('bar', $string);
    }

    #[Test]
    public function canExplodeString(): void
    {
        $string = Strings::explode('foo bar foo', ' ');
        $this->assertSame(['foo', 'bar', 'foo'], $string);

        $string = Strings::explode('foo bar foo', ' ', -1);
        $this->assertSame(['foo', 'bar'], $string);
    }

    #[Test]
    public function canGenerateRandomWords(): void
    {
        $string = Strings::randomStrings($words = 5, $size = 5);

        $result = ($words * $size) + ($words) - 1;
        $this->assertSame($result, \strlen($string));
    }

    #[Test]
    public function canConvertToSnakeCase(): void
    {
        $string = Strings::toSnakeCase('thisIsAString');

        $this->assertSame('this_is_a_string', $string);
    }

    #[Test]
    public function canConvertToCamelCase(): void
    {
        $string = Strings::toCamelCase('this_is_a_string');

        $this->assertSame('thisIsAString', $string);
    }

    #[Test]
    public function canConvertToPascalCase(): void
    {
        $string = Strings::toPascalCase('this_is_a_string');

        $this->assertSame('ThisIsAString', $string);
    }

    #[Test]
    public function canConvertToLowercase(): void
    {
        $this->assertSame('taylor', Strings::lower('TAYLOR'));
        $this->assertSame('άχιστη', Strings::lower('ΆΧΙΣΤΗ'));
    }

    #[Test]
    public function canConvertToUppercase(): void
    {
        $this->assertSame('TAYLOR', Strings::upper('taylor'));
        $this->assertSame('ΆΧΙΣΤΗ', Strings::upper('άχιστη'));
    }

    #[Test]
    public function canConvertToTitleCase(): void
    {
        $this->assertSame('Taylor', Strings::title('taylor'));
        $this->assertSame('Άχιστη', Strings::title('άχιστη'));
    }

    #[Test]
    public function canLimitStringsByCharacters(): void
    {
        $this->assertSame('Tay...', Strings::limit('Taylor', 3));
        $this->assertSame('Taylor', Strings::limit('Taylor', 6));
        $this->assertSame('Tay___', Strings::limit('Taylor', 3, '___'));
    }

    #[Test]
    public function canLimitByWords(): void
    {
        $this->assertSame('Taylor...', Strings::words('Taylor Otwell', 1));
        $this->assertSame('Taylor___', Strings::words('Taylor Otwell', 1, '___'));
        $this->assertSame('Taylor Otwell', Strings::words('Taylor Otwell', 3));
    }

    #[Test]
    public function canCheckIfIsIp(): void
    {
        $this->assertTrue(Strings::isIp('192.168.1.1'));
        $this->assertFalse(Strings::isIp('foobar'));
    }

    #[Test]
    public function canCheckIfIsEmail(): void
    {
        $this->assertTrue(Strings::isEmail('foo@bar.com'));
        $this->assertFalse(Strings::isEmail('foobar'));
    }

    #[Test]
    public function canCheckIfIsUrl(): void
    {
        $this->assertTrue(Strings::isUrl('http://www.foo.com/'));
        $this->assertFalse(Strings::isUrl('foobar'));
    }

    #[Test]
    public function canPrependString(): void
    {
        $this->assertSame('foobar', Strings::prepend('bar', 'foo'));
    }

    #[Test]
    public function canAppendString(): void
    {
        $this->assertSame('foobar', Strings::append('foo', 'bar'));
    }

    #[Test]
    public function canGetBaseClass(): void
    {
        $this->assertSame('Baz', Strings::baseClass(Baz::class));
    }
}
