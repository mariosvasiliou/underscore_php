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
use stdClass;

/**
 * Class DispatchTest
 *
 * @package Underscore
 */
class DispatchTest extends UnderscoreTestCase
{

    // Data providers ------------------------------------------------ /

    /**
     * @return array
     */
    public static function provideTypes(): \Iterator
    {
        yield ['string', 'Strings'];
        yield [5.14, 'Number'];
        yield [512, 'Number'];
        yield [1.2e3, 'Number'];
        yield [7E-10, 'Number'];
        yield [[], 'Arrays'];
        yield [new stdClass(), 'BaseObject'];
        yield [
            function(): void {
            },
            'Functions',
        ];
        yield [null, 'Strings'];
    }

    /**
     *
     * @param $subject
     * @param $expected
     */
    #[DataProvider('provideTypes')]
    #[Test]
    public function canGetClassFromType($subject, string $expected): void
    {
        $dispatch = Dispatch::toClass($subject);

        $this->assertSame('Underscore\Types\\'.$expected, $dispatch);
    }

    #[Test]
    public function canThrowExceptionAtUnknownTypes(): void
    {
        $this->expectException('InvalidArgumentException');

        $file     = fopen('../.travis.yml', 'w+');
        Dispatch::toClass($file);
    }
}
