<?php
declare(strict_types=1);

use Underscore\Underscore;

/**
 * Shortcut alias to creating an Underscore object
 *
 * @param mixed $type A scalar type to wrap
 *
 * @return Underscore
 */
if ( ! function_exists('underscore')) {
    /**
     * @param $type
     *
     * @return Underscore
     */
    function underscore($type)
    {
        return new Underscore($type);
    }
}

/**
 * Shortcut alias for underscore()
 *
 * @param mixed $type
 *
 * @return Underscore
 */
if (!function_exists('__')) {
    /**
     * @param $type
     *
     * @return Underscore
     */
    function __($type)
    {
        return underscore($type);
    }
}
