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
    public function provideTypes() : array
    {
        return [
            ['string', 'Strings'],
            [5.14, 'Number'],
            [512, 'Number'],
            [1.2e3, 'Number'],
            [7E-10, 'Number'],
            [[], 'Arrays'],
            [new stdClass(), 'BaseObject'],
            [
                function() {
                    return;
                },
                'Functions',
            ],
            [null, 'Strings'],
        ];
    }

    /**
     * @dataProvider provideTypes
     *
     * @param $subject
     * @param $expected
     */
    public function testCanGetClassFromType($subject, $expected) : void
    {
        $dispatch = Dispatch::toClass($subject);

        $this->assertEquals('Underscore\Types\\'.$expected, $dispatch);
    }

    public function testCanThrowExceptionAtUnknownTypes() : void
    {
        $this->expectException('InvalidArgumentException');

        $file     = fopen('../.travis.yml', 'w+');
        $dispatch = Dispatch::toClass($file);
    }
}
