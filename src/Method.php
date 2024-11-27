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

/**
 * Various helpers relatives to methods.
 *
 * @see \Underscore\MethodTest
 */
class Method
{
    /**
     * A list of methods to automatically defer to PHP.
     */
    public static array $defer = [
        'trim',
        'count',
        'round',
        'ceil',
        'floor',
        'substr',
        'str_pad' => 'pad',
        'ucfirst',
        'lcfirst',
        'ucwords',
        'strtolower',
        'strtoupper',
    ];

    /**
     * A list of methods where the subject
     * isn't to be added to the arguments.
     */
    protected static array $subjectless = [
        'fill',
    ];

    /**
     * A list of methods that are allowed
     * to break the chain.
     */
    protected static array $breakers = [
        'get',
        'sum',
        'count',
        'fromJSON',
        'toJSON',
        'fromXML',
        'fromCSV',
        'toCSV',
    ];

    /**
     * Unchainable methods.
     */
    protected static array $unchainable = [
        'Arrays::range',
        'Arrays::repeat',
    ];


    ////////////////////////////////////////////////////////////////////
    ////////////////////////////// HELPERS /////////////////////////////
    ////////////////////////////////////////////////////////////////////

    /**
     * Get the Methods class from the Type class.
     *
     * @param string $class The Type class
     *
     * @return string The Methods class
     */
    public static function getMethodsFromType(string $class) : string
    {
        return str_replace('Types', 'Methods', $class.'Methods');
    }

    /**
     * Whether a native method requires a subject or not.
     *
     * @param  string  $method  The function
     */
    public static function isSubjectless(string $method) : bool
    {
        return \in_array($method, static::$subjectless, true);
    }

    /**
     * Whether a method should not be chained.
     *
     * @param  string  $class  The class
     * @param string $method The method
     */
    public static function isUnchainable(string $class, string $method) : bool
    {
        $class = str_replace('Underscore\Types\\', '', $class);

        return \in_array($class.'::'.$method, static::$unchainable, true);
    }

    /**
     * Whether a method is a breaker.
     *
     * @param  string  $method  The method
     */
    public static function isBreaker(string $method) : bool
    {
        return \in_array($method, static::$breakers, true);
    }

    /**
     * Get a method name by its alias.
     *
     * @param string $method The method
     *
     * @return string|null The real method name
     */
    public static function getAliasOf(string $method) : ?string
    {
        return Underscore::option('aliases.'.$method);
    }

    /**
     * Get the native function corresponding to a method.
     *
     * @param  string  $method  The method to look for
     *
     * @return string|false The native function
     */
    public static function getNative(string $method) : bool|string
    {
        // If a defered method exist
        if (\in_array($method, static::$defer, true)) {
            $native = array_search($method, static::$defer, true);

            return \is_int($native) ? $method : $native;
        }

        return false;
    }

    /**
     * Find a method in the type classes.
     *
     * @param  string  $originalClass  The class calling the method
     * @param  string  $method  The method
     *
     * @return string The class name
     */
    public static function findInClasses(string $originalClass, string $method) : string
    {
        $classes = ['Arrays', 'Collection', 'Functions', 'Number', 'BaseObject', 'Strings'];
        foreach ($classes as $class) {
            if (method_exists('\Underscore\Methods\\'.$class.'Methods', $method)) {
                return '\Underscore\Types\\'.$class;
            }
        }

        return $originalClass;
    }
}
