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

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Underscore\Dummies\DummyDefault;

/**
 * Class ParseTest
 *
 * @package Underscore
 */
class ParseTest extends UnderscoreTestCase
{

    ////////////////////////////////////////////////////////////////////
    ////////////////////////// DATA PROVIDERS //////////////////////////
    ////////////////////////////////////////////////////////////////////

    /**
     * @return array
     */
    public function provideSwitchers(): \Iterator
    {
        yield ['toArray', null, []];
        yield ['toArray', 15, [15]];
        yield ['toArray', 'foobar', ['foobar']];
        yield ['toArray', (object) $this->array, $this->array];
        yield ['toArray', new DummyDefault(), ['foo', 'bar']];
        yield ['toString', 15, '15'];
        yield ['toString', ['foo', 'bar'], '["foo","bar"]'];
        yield ['toInteger', 'foo', 3];
        yield ['toInteger', '', 0];
        yield ['toInteger', '15', 15];
        yield ['toInteger', [1, 2, 3], 3];
        yield ['toInteger', [], 0];
        yield ['toObject', $this->array, (object) $this->array];
        yield ['toBoolean', '', false];
        yield ['toBoolean', 'foo', true];
        yield ['toBoolean', 15, true];
        yield ['toBoolean', 0, false];
        yield ['toBoolean', [], false];
    }

    ////////////////////////////////////////////////////////////////////
    ////////////////////////////// TESTS ///////////////////////////////
    ////////////////////////////////////////////////////////////////////

    #[Test]
    public function canCreateCsvFiles(): void
    {
        $csv     = Parse::toCSV($this->arrayMulti);
        $matcher = '"bar";"ter"'.PHP_EOL.'"bar";"ter"'.PHP_EOL.'"foo";"ter"';

        $this->assertEquals($matcher, $csv);
    }

    #[Test]
    public function canUseCustomCsvDelimiter(): void
    {
        $csv     = Parse::toCSV($this->arrayMulti, ',');
        $matcher = '"bar","ter"'.PHP_EOL.'"bar","ter"'.PHP_EOL.'"foo","ter"';

        $this->assertEquals($matcher, $csv);
    }

    #[Test]
    public function canOutputCsvHeaders(): void
    {
        $csv     = Parse::toCSV($this->arrayMulti, ',', true);
        $matcher = 'foo,bis'.PHP_EOL.'"bar","ter"'.PHP_EOL.'"bar","ter"'.PHP_EOL.'"foo","ter"';

        $this->assertEquals($matcher, $csv);
    }

    #[Test]
    public function canConvertToJson(): void
    {
        $json    = Parse::toJSON($this->arrayMulti);
        $matcher = '[{"foo":"bar","bis":"ter"},{"foo":"bar","bis":"ter"},{"bar":"foo","bis":"ter"}]';

        $this->assertSame($matcher, $json);
    }

    #[Test]
    public function canParseJson(): void
    {
        $json  = Parse::toJSON($this->arrayMulti);
        $array = Parse::fromJSON($json);

        $this->assertEquals($this->arrayMulti, $array);
    }

    #[Test]
    public function canParseXML(): void
    {
        $array   = Parse::fromXML('<article><name>foo</name><content>bar</content></article>');
        $matcher = ['name' => 'foo', 'content' => 'bar'];

        $this->assertSame($matcher, $array);
    }

    #[Test]
    public function canParseCSV(): void
    {
        $array   = Parse::fromCSV("foo;bar;bis\nbar\tfoo\tter");
        $results = [['foo', 'bar', 'bis'], ['bar', 'foo', 'ter']];

        $this->assertSame($results, $array);
    }

    /**
     * @param string $value
     */
    #[Test]
    public function canParseCSVWithHeaders($value = ''): void
    {
        $array   = Parse::fromCSV('foo;bar;bis'.PHP_EOL."bar\tfoo\tter", true);
        $results = [['foo' => 'bar', 'bar' => 'foo', 'bis' => 'ter']];

        $this->assertSame($results, $array);
    }

    ////////////////////////////////////////////////////////////////////
    ///////////////////////// TYPES SWITCHERS //////////////////////////
    ////////////////////////////////////////////////////////////////////
    /**
     *
     * @param $method
     * @param $from
     * @param $to
     */
    #[DataProvider('provideSwitchers')]
    #[Test]
    public function canSwitchTypes($method, $from, $to): void
    {
        $from = Parse::$method($from);

        $this->assertEquals($to, $from);
    }
}
