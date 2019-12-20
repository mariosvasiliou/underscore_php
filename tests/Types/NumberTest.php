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
 * Class NumberTest
 *
 * @package Underscore\Types
 */
class NumberTest extends UnderscoreTestCase
{

    public function testCanCreateNewNumber() : void
    {
        $this->assertEquals(0, Number::create()->obtain());
    }

    public function testCanAccessStrPadding() : void
    {
        $number = Number::padding(5, 3, \STR_PAD_LEFT);

        $this->assertEquals('005', $number);
    }

    public function testCanPadANumber() : void
    {
        $number = Number::padding(5, 3);

        $this->assertEquals('050', $number);
    }

    public function testCanPadANumberOnTheLeft() : void
    {
        $number = Number::paddingLeft(5, 3);

        $this->assertEquals('005', $number);
    }

    public function testCanPadANumberOnTheRight() : void
    {
        $number = Number::paddingRight(5, 3);

        $this->assertEquals('500', $number);
    }

    public function testCanUsePhpRoundingMethods() : void
    {
        $number = Number::round(5.33);
        $this->assertEquals(5, $number);

        $number = Number::ceil(5.33);
        $this->assertEquals(6, $number);

        $number = Number::floor(5.33);
        $this->assertEquals(5, $number);
    }
}
