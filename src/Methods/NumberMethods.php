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

namespace Underscore\Methods;

/**
 * Methods to manage numbers.
 */
class NumberMethods
{

    /**
     * Add 0 padding to an integer.
     *
     * @param     $number
     *
     */
    public static function padding(mixed $number, int $padding = 1, int $direction = STR_PAD_BOTH) : string
    {
        return str_pad((string) $number, $padding, '0', $direction);
    }

    /**
     * Add 0 padding on the left of an integer.
     *
     * @param     $number
     *
     */
    public static function paddingLeft($number, int $padding = 1) : string
    {
        return static::padding($number, $padding, STR_PAD_LEFT);
    }

    /**
     * Add 0 padding on the right of an integer.
     *
     * @param     $number
     *
     */
    public static function paddingRight($number, int $padding = 1) : string
    {
        return static::padding($number, $padding, STR_PAD_RIGHT);
    }
}
