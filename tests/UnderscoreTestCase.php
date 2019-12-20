<?php

/*
 * This file is part of Underscore.php
 *
 * (c) Maxime Fabre <ehtnam6@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Underscore;


use PHPUnit\Framework\TestCase;

/**
 * Class UnderscoreTestCase
 *
 * @package Underscore
 */
abstract class UnderscoreTestCase extends TestCase
{

    public $array        = ['foo' => 'bar', 'bis' => 'ter'];
    public $arrayNumbers = [1, 2, 3];
    public $arrayMulti   = [
        ['foo' => 'bar', 'bis' => 'ter'],
        ['foo' => 'bar', 'bis' => 'ter'],
        ['bar' => 'foo', 'bis' => 'ter'],
    ];
    public $object;
    /**
     * @var object
     */
    public $objectMulti;

    /**
     * Restore data just in case.
     */
    public function setUp()
    {
        $this->object      = (object)$this->array;
        $this->objectMulti = (object)[
            (object)$this->arrayMulti[0],
            (object)$this->arrayMulti[1],
            (object) $this->arrayMulti[2],
        ];
    }
}
