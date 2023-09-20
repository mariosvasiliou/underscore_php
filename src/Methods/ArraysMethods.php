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

use Closure;

/**
 * Methods to manage arrays.
 */
class ArraysMethods extends CollectionMethods
{

    ////////////////////////////////////////////////////////////////////
    ///////////////////////////// GENERATE /////////////////////////////
    ////////////////////////////////////////////////////////////////////
    //<editor-fold desc="*** Methods ***">
    /**
     * Generate an array from a range.
     *
     * @param  int  $_base  The base number
     * @param  int|null  $stop  The stopping point
     * @param  int  $step  How many to increment of
     */
    public static function range(int $_base, int $stop = null, int $step = 1) : array
    {
        // Dynamic arguments
        if ($stop !== null) {
            $start = $_base;
        } else {
            $start = 1;
            $stop  = $_base;
        }

        return range($start, $stop, $step);
    }

    /**
     * Fill an array with $times times some $data.
     */
    public static function repeat(mixed $data, int $times) : array
    {
        $timesAbs = abs($times);
        if ($timesAbs === 0) {
            return [];
        }

        return array_fill(0, $timesAbs, $data);
    }

    ////////////////////////////////////////////////////////////////////
    ///////////////////////////// ANALYZE //////////////////////////////
    ////////////////////////////////////////////////////////////////////

    /**
     * Search for the index of a value in an array.
     *
     * @param  array  $array
     * @param  string  $value
     *
     * @return int|string|bool
     */
    public static function search(array $array, mixed $value) : int|string|bool
    {
        return array_search($value, $array, true);
    }

    /**
     * Check if all items in an array match a truth test.
     *
     * @param  array  $array
     * @param  callable  $closure
     *
     * @return bool
     */
    public static function matches(array $array, callable $closure) : bool
    {
        // Reduce the array to only booleans
        $array = static::each($array, $closure);

        // Check the results
        if (\count($array) === 0) {
            return true;
        }
        $result = array_search(false, $array, false);

        return \is_bool($result);
    }

    /**
     * Check if any item in an array matches a truth test.
     */
    public static function matchesAny(array $array, callable $closure) : bool
    {
        // Reduce the array to only booleans
        $array = static::each($array, $closure);

        // Check the results
        if (\count($array) === 0) {
            return true;
        }
        $result = array_search(true, $array, false);

        return \is_int($result);
    }

    /**
     * Check if an item is in an array.
     *
     * @param $array
     * @param $value
     *
     * @return bool
     */
    public static function contains(array $array, mixed $value) : bool
    {
        return \in_array($value, $array, true);
    }

    /**
     * Returns the average value of an array.
     *
     * @param array $array    The source array
     * @param int   $decimals The number of decimals to return
     *
     * @return float The average value
     */
    public static function average(array $array, int $decimals = 0) : float
    {
        return round((array_sum($array) / \count($array)), $decimals);
    }

    /**
     * Get the size of an array.
     */
    public static function size(array $array) : int
    {
        return \count($array);
    }

    /**
     * Get the max value from an array.
     *
     * @param  array  $array
     * @param  Closure|null  $closure
     *
     * @return mixed
     */
    public static function max(array $array, Closure $closure = null) : mixed
    {
        // If we have a closure, apply it to the array
        if ($closure instanceof \Closure) {
            $array = static::each($array, $closure);
        }

        return max($array);
    }

    /**
     * Get the min value from an array.
     *
     * @param  array  $array
     * @param  Closure|null  $closure
     *
     * @return mixed
     */
    public static function min(array $array, Closure $closure = null) : mixed
    {
        // If we have a closure, apply it to the array
        if ($closure instanceof \Closure) {
            $array = static::each($array, $closure);
        }

        return min($array);
    }

    ////////////////////////////////////////////////////////////////////
    //////////////////////////// FETCH FROM ////////////////////////////
    ////////////////////////////////////////////////////////////////////
    /**
     * Find the first item in an array that passes the truth test.
     *
     * @return mixed|void
     */
    public static function find(array $array, Closure $closure)
    {
        foreach ($array as $key => $value) {
            if ($closure($value, $key)) {
                return $value;
            }
        }
    }

    /**
     * Clean all falsy values from an array.
     *
     * @return array|mixed
     */
    public static function clean(array $array) : mixed
    {
        return static::filter($array, fn($value) : bool => (bool) $value);
    }

    /**
     * Get a random string from an array.
     *
     * @param int|null $take
     * @return array|mixed
     */
    public static function random(array $array, int $take = null) : mixed
    {
        if ($take === null) {
            return $array[array_rand($array)];
        }

        shuffle($array);

        return static::first($array, $take);
    }

    /**
     * Return an array without all instances of certain values.
     */
    public static function without(...$arguments)
    {
        $array     = array_shift($arguments);
        // if singular argument and is an array treat this AS the array to run without agains
        if (\is_array($arguments[0]) && \count($arguments) === 1) {
            $arguments = $arguments[0];
        }

        return static::filter($array, fn($value) : bool => ! \in_array($value, $arguments, true));
    }

    /**
     * Return an array with all elements found in both input arrays.
     */
    public static function intersection(array $a, array $b) : array
    {
        return array_values(array_intersect($a, $b));
    }

    /**
     * Return a boolean flag which indicates whether the two input arrays have any common elements.
     */
    public static function intersects(array $a, array $b) : bool
    {
        return \count(self::intersection($a, $b)) > 0;
    }

    ////////////////////////////////////////////////////////////////////
    ///////////////////////////// SLICERS //////////////////////////////
    ////////////////////////////////////////////////////////////////////
    /**
     * Get the first value from an array.
     *
     * @param int|null $take
     * @return array|mixed
     */
    public static function first(array $array, int $take = null) : mixed
    {
        if ($take === null) {
            return array_shift($array);
        }

        return array_splice($array, 0, $take, true);
    }

    /**
     * Get the last value from an array.
     *
     * @param int|null $take
     * @return array|mixed
     */
    public static function last(array $array, int $take = null) : mixed
    {
        if ($take === null) {
            return array_pop($array);
        }

        return static::rest($array, -$take);
    }

    /**
     * Get everything but the last $to items.
     *
     * @param  array  $array
     * @param  int  $to
     *
     * @return array|mixed
     */
    public static function initial(array $array, int $to = 1) : mixed
    {
        $slice = \count($array) - $to;

        return static::first($array, $slice);
    }

    /**
     * Get the last elements from index $from.
     *
     * @param  array  $array
     * @param  int  $from
     *
     * @return array
     */
    public static function rest(array $array, int $from = 1) : array
    {
        return array_splice($array, $from);
    }

    ////////////////////////////////////////////////////////////////////
    ///////////////////////////// ACT UPON /////////////////////////////
    ////////////////////////////////////////////////////////////////////
    /**
     * Iterate over an array and execute a callback for each loop.
     */
    public static function at(array $array, Closure $closure) : array
    {
        foreach ($array as $key => $value) {
            $closure($value, $key);
        }

        return $array;
    }

    ////////////////////////////////////////////////////////////////////
    ////////////////////////////// ALTER ///////////////////////////////
    ////////////////////////////////////////////////////////////////////
    /**
     * Replace a value in an array.
     *
     * @param array  $array   The array
     * @param string $replace The string to replace
     * @param string $with    What to replace it with
     */
    public static function replaceValue(array $array, string $replace, string $with) : array
    {
        return static::each($array, fn($value) : string|array => str_replace($replace, $with, (string) $value));
    }

    /**
     * Replace the keys in an array with another set.
     *
     * @param array $array The array
     * @param array $keys  An array of keys matching the array's size
     */
    public static function replaceKeys(array $array, array $keys) : array
    {
        $values = array_values($array);

        return array_combine($keys, $values);
    }

    /**
     * Iterate over an array and modify the array's value.
     */
    public static function each(array $array, Closure $closure) : array
    {
        foreach ($array as $key => $value) {
            $array[$key] = $closure($value, $key);
        }

        return $array;
    }

    /**
     * Shuffle an array.
     */
    public static function shuffle(array $array) : array
    {
        shuffle($array);

        return $array;
    }

    /**
     * Sort an array by key.
     */
    public static function sortKeys(array $array, string $direction = 'ASC') : array
    {
        $directionNumber = (strtolower($direction) === 'desc') ? SORT_DESC : SORT_ASC;
        if ($directionNumber === SORT_ASC) {
            ksort($array);
        } else {
            krsort($array);
        }

        return $array;
    }

    /**
     * Implodes an array.
     *
     * @param array  $array The array
     * @param string $with  What to implode it with
     */
    public static function implode(array $array, string $with = '') : string
    {
        return implode($with, $array);
    }

    /**
     * Find all items in an array that pass the truth test.
     *
     * @param  array  $array
     * @param  Closure|null  $closure
     *
     * @return array|mixed
     */
    public static function filter(array $array, Closure $closure = null) : mixed
    {
        if ($closure === null) {
            return static::clean($array);
        }

        return array_filter($array, $closure);
    }

    /**
     * Flattens an array to dot notation.
     *
     * @param  array  $array  An array
     * @param  string  $separator  The characater to flatten with
     * @param  string|null  $parent  The parent passed to the child (private)
     *
     * @return array Flattened array to one level
     */
    public static function flatten(array $array, string $separator = '.', string $parent = null) : array
    {
        $_flattened = [];

        // Rewrite keys
        foreach ($array as $key => $value) {
            if ($parent) {
                $key = $parent.$separator.$key;
            }

            if ( ! \is_array($value)) {
                $_flattened[$key] = $value;
                continue;
            }

            $_flattened[$key] = static::flatten($value, $separator, $key);
        }

        // Flatten
        $flattened = [];
        foreach ($_flattened as $key => $value) {
            if (\is_array($value)) {
                $flattened = [...$flattened, ...$value];
            } else {
                $flattened[$key] = $value;
            }
        }

        return $flattened;
    }

    /**
     * Invoke a function on all of an array's values.
     *
     * @param  array  $array
     * @param  Closure|string  $callable  $callable
     * @param  array  $arguments
     *
     * @return array
     */
    public static function invoke(array $array, Closure|string $callable, mixed $arguments = []) : array
    {
        // If one argument given for each iteration, create an array for it
        if ( ! \is_array($arguments)) {
            $arguments = static::repeat($arguments, \count($array));
        }

        // If the callable has arguments, pass them
        if ($arguments) {
            return array_map($callable, $array, $arguments);
        }

        return array_map($callable, $array);
    }

    /**
     * Return all items that fail the truth test.
     */
    public static function reject(array $array, Closure $closure) : array
    {
        $filtered = [];

        foreach ($array as $key => $value) {
            if ( ! $closure($value, $key)) {
                $filtered[$key] = $value;
            }
        }

        return $filtered;
    }

    /**
     * Remove the first value from an array.
     */
    public static function removeFirst(array $array) : array
    {
        array_shift($array);

        return $array;
    }

    /**
     * Remove the last value from an array.
     */
    public static function removeLast(array $array) : array
    {
        array_pop($array);

        return $array;
    }

    /**
     * Removes a particular value from an array (numeric or associative).
     */
    public static function removeValue(array $array, string $value) : array
    {
        $isNumericArray = true;
        foreach ($array as $key => $item) {
            if ($item === $value) {
                if ( ! \is_int($key)) {
                    $isNumericArray = false;
                }

                unset($array[$key]);
            }
        }

        if ($isNumericArray) {
            return array_values($array);
        }

        return $array;
    }

    /**
     * Prepend a value to an array.
     *
     * @param  array  $array
     * @param  string  $value
     *
     * @return array
     */
    public static function prepend(array $array, mixed $value) : array
    {
        array_unshift($array, $value);

        return $array;
    }

    /**
     * Append a value to an array.
     *
     * @param string $value
     */
    public static function append(array $array, mixed $value) : array
    {
        $array[] = $value;

        return $array;
    }

    /**
     * Return a duplicate free copy of an array
     *
     * @param  array  $array
     *
     * @return array
     */
    public static function unique(array $array) : array
    {
        return array_reduce($array, function (array $resultArray, $value) : array {
            if ( ! static::contains($resultArray, $value)) {
                $resultArray[] = $value;
            }

            return $resultArray;
        }, []);
    }

    //</editor-fold desc="*** Methods ***">
}
