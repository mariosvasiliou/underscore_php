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

namespace Underscore\Dummies;

use Underscore\Types\Arrays;

/**
 * Class DummyClass
 *
 * @package Underscore\Dummies
 */
class DummyClass extends Arrays
{
    /**
     * Get the core data.
     *
     * @return self
     */
    public function getUsers() : self
    {
        $users = [
            ['foo' => 'bar'],
            ['bar' => 'foo'],
        ];

        return $this->setSubject($users);
    }

    /**
     * Overwrite of the map method.
     *
     * @param mixed $whatever
     *
     * @return self
     */
    public function map(mixed $whatever) : self
    {
        $this->subject = $whatever * 3;

        return $this;
    }
}
