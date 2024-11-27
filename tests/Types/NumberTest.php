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
use Underscore\UnderscoreTestCase;

/**
 * Class NumberTest
 *
 * @package Underscore\Types
 */
class NumberTest extends UnderscoreTestCase
{

    #[Test]
    public function canCreateNewNumber(): void
    {
        $this->assertSame(0, Number::create()->obtain());
    }

    #[Test]
    public function canAccessStrPadding(): void
    {
        $number = Number::padding(5, 3, \STR_PAD_LEFT);

        $this->assertSame('005', $number);
    }

    #[Test]
    public function canPadANumber(): void
    {
        $number = Number::padding(5, 3);

        $this->assertSame('050', $number);
    }

    #[Test]
    public function canPadANumberOnTheLeft(): void
    {
        $number = Number::paddingLeft(5, 3);

        $this->assertSame('005', $number);
    }

    #[Test]
    public function canPadANumberOnTheRight(): void
    {
        $number = Number::paddingRight(5, 3);

        $this->assertSame('500', $number);
    }

    #[Test]
    public function canUsePhpRoundingMethods(): void
    {
        $number = Number::round(5.33);
        $this->assertEqualsWithDelta(5.0, $number, PHP_FLOAT_EPSILON);

        $number = Number::ceil(5.33);
        $this->assertEqualsWithDelta(6.0, $number, PHP_FLOAT_EPSILON);

        $number = Number::floor(5.33);
        $this->assertEqualsWithDelta(5.0, $number, PHP_FLOAT_EPSILON);
    }
}
