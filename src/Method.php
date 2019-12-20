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

/**
 * Various helpers relatives to methods.
 */
class Method
{
    /**
     * A list of methods to automatically defer to PHP.
     *
     * @var array
     */
    public static $defer = [
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
     *
     * @var array
     */
    protected static $subjectless = [
        'fill',
    ];

    /**
     * A list of methods that are allowed
     * to break the chain.
     *
     * @var array
     */
    protected static $breakers = [
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
     *
     * @var array
     */
    protected static $unchainable = [
        'Arrays::range',
        'Arrays::repeat',
    ];

    /**
     * A cache for better findInClasses performances.
     *
     * @var array
     */
    protected static $findCache = [];

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
    public static function getMethodsFromType($class) : string
    {
        return str_replace('Types', 'Methods', $class.'Methods');
    }

    /**
     * Whether a native method requires a subject or not.
     *
     * @param string $method The function
     *
     * @return bool
     */
    public static function isSubjectless($method) : bool
    {
        return \in_array($method, static::$subjectless, true);
    }

    /**
     * Whether a method should not be chained.
     *
     * @param string $class  The class
     * @param string $method The method
     *
     * @return bool
     */
    public static function isUnchainable($class, $method) : bool
    {
        $class = str_replace('Underscore\Types\\', null, $class);

        return \in_array($class.'::'.$method, static::$unchainable, true);
    }

    /**
     * Whether a method is a breaker.
     *
     * @param string $method The method
     *
     * @return bool
     */
    public static function isBreaker($method) : bool
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
    public static function getAliasOf($method) : ?string
    {
        return Underscore::option('aliases.'.$method);
    }

    /**
     * Get the native function corresponding to a method.
     *
     * @param string $method The method to look for
     *
     * @return string The native function
     */
    public static function getNative($method) : string
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
     * @param string $originalClass The class calling the method
     * @param string $method        The method
     *
     * @return string The class name
     */
    public static function findInClasses($originalClass, $method) : string
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
