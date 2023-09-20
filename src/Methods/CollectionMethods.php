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
 * Abstract Collection type
 * Methods that apply to both objects and arrays.
 */
abstract class CollectionMethods
{
    ////////////////////////////////////////////////////////////////////
    ///////////////////////////// ANALYZE //////////////////////////////
    ////////////////////////////////////////////////////////////////////
    /**
     * Check if an array has a given key.
     *
     * @param  array  $array
     * @param  string  $key
     *
     * @return bool
     */
    public static function has(mixed $array, string $key) : bool
    {
        // Generate unique string to use as marker
        $unfound = StringsMethods::random(5);

        return static::get($array, $key, $unfound) !== $unfound;
    }

    ////////////////////////////////////////////////////////////////////
    //////////////////////////// FETCH FROM ////////////////////////////
    ////////////////////////////////////////////////////////////////////

    /**
     * Get a value from an collection using dot-notation.
     *
     * @param  array  $collection  The collection to get from
     * @param  string|int|null  $key  The key to look for
     * @param  mixed  $default  Default value to fallback to
     *
     * @return mixed
     */
    public static function get(mixed $collection, mixed $key = null, mixed $default = null) : mixed
    {
        if ($key === null) {
            return $collection;
        }

        $collection = (array) $collection;

        if (isset($collection[$key])) {
            return $collection[$key];
        }

        // Crawl through collection, get key according to object or not
        foreach (explode('.', (string) $key) as $segment) {
            $collection = (array) $collection;

            if (!isset($collection[$segment])) {
                return $default instanceof Closure ? $default() : $default;
            }

            $collection = $collection[$segment];
        }

        return $collection;
    }

    /**
     * Set a value in a collection using dot notation.
     *
     * @param mixed  $collection The collection
     * @param string $key        The key to set
     * @param mixed  $value      Its value
     *
     * @return mixed
     */
    public static function set(mixed $collection, string $key, mixed $value) : mixed
    {
        static::internalSet($collection, $key, $value);

        return $collection;
    }

    /**
     * Get a value from a collection and set it if it wasn't.
     *
     * @param mixed  $collection The collection
     * @param string $key        The key
     * @param mixed  $default    The default value to set if it isn't
     *
     * @return mixed
     */
    public static function setAndGet(mixed &$collection, string $key, mixed $default = null) : mixed
    {
        // If the key doesn't exist, set it
        if (!static::has($collection, $key)) {
            $collection = static::set($collection, $key, $default);
        }

        return static::get($collection, $key);
    }

    /**
     * Remove a value from an array using dot notation.
     *
     * @param  mixed  $collection
     * @param  string|array  $key
     *
     * @return mixed
     */
    public static function remove(mixed $collection, string|array $key) : mixed
    {
        // Recursive call
        if (\is_array($key)) {
            foreach ($key as $k) {
                static::internalRemove($collection, $k);
            }

            return $collection;
        }

        static::internalRemove($collection, $key);

        return $collection;
    }

    /**
     * Fetches all columns $property from a multimensionnal array.
     *
     * @param $collection
     * @param $property
     *
     * @return array|object
     */
    public static function pluck($collection, $property) : object|array
    {
        $plucked = array_map(fn($value) => ArraysMethods::get($value, $property), (array) $collection);

        // Convert back to object if necessary
        if (\is_object($collection)) {
            return (object) $plucked;
        }

        return $plucked;
    }

    /**
     * Filters an array of objects (or a numeric array of associative arrays) based on the value of a particular
     * property within that.
     *
     * @param              $collection
     * @param  string  $property
     * @param  mixed  $value
     * @param  string|null  $comparisonOp
     *
     * @return array|object
     */
    public static function filterBy($collection, string $property, mixed $value, string $comparisonOp = null) :
    object|array
    {
        if (!$comparisonOp) {
            $comparisonOp = \is_array($value) ? 'contains' : 'eq';
        }

        $ops = [
            'eq'          => fn($item, $prop, $value) : bool => $item[$prop] === $value,
            'gt'          => fn($item, $prop, $value) : bool => $item[$prop] > $value,
            'gte'         => fn($item, $prop, $value) : bool => $item[$prop] >= $value,
            'lt'          => fn($item, $prop, $value) : bool => $item[$prop] < $value,
            'lte'         => fn($item, $prop, $value) : bool => $item[$prop] <= $value,
            'ne'          => fn($item, $prop, $value) : bool => $item[$prop] !== $value,
            'contains'    => fn($item, $prop, $value) : bool => \in_array($item[$prop], (array) $value, true),
            'notContains' => fn($item, $prop, $value) : bool => ! \in_array($item[$prop], (array) $value, true),
            'newer'       => fn(
                $item,
                $prop,
                $value
            ) : bool => strtotime((string) $item[$prop]) > strtotime((string) $value),
            'older'       => fn(
                $item,
                $prop,
                $value
            ) : bool => strtotime((string) $item[$prop]) < strtotime((string) $value),
        ];
        $result = array_values(array_filter((array) $collection, function ($item) use (
            $property,
            $value,
            $ops,
            $comparisonOp
        ) {
            $item = (array) $item;
            $item[$property] = static::get($item, $property, []);

            return $ops[$comparisonOp]($item, $property, $value);
        }));
        if (\is_object($collection)) {
            return (object) $result;
        }

        return $result;
    }

    /**
     * @param        $collection
     * @param        $property
     * @param        $value
     * @param  string  $comparisonOp
     *
     * @return array|mixed
     */
    public static function findBy($collection, $property, $value, string $comparisonOp = 'eq') : mixed
    {
        $filtered = static::filterBy($collection, $property, $value, $comparisonOp);

        return ArraysMethods::first($filtered);
    }

    ////////////////////////////////////////////////////////////////////
    ///////////////////////////// ANALYZE //////////////////////////////
    ////////////////////////////////////////////////////////////////////
    /**
     * Get all keys from a collection.
     *
     * @param $collection
     *
     * @return array
     */
    public static function keys($collection) : array
    {
        return array_keys((array) $collection);
    }

    /**
     * Get all values from a collection.
     *
     * @param $collection
     *
     * @return array
     */
    public static function values($collection) : array
    {
        return array_values((array) $collection);
    }

    ////////////////////////////////////////////////////////////////////
    ////////////////////////////// ALTER ///////////////////////////////
    ////////////////////////////////////////////////////////////////////

    /**
     * Replace a key with a new key/value pair.
     *
     * @param  array|object  $collection
     * @param  string  $replace
     * @param  string  $key
     * @param  mixed  $value
     *
     * @return mixed
     */
    public static function replace(array|object $collection, string $replace, string $key, mixed $value) : mixed
    {
        $collection = static::remove($collection, $replace);

        return static::set($collection, $key, $value);
    }

    /**
     * Sort a collection by value, by a closure or by a property
     * If the sorter is null, the collection is sorted naturally.
     *
     * @param  array|object  $collection
     * @param  null  $sorter
     * @param  string  $direction
     *
     * @return array
     */
    public static function sort(array|object $collection, $sorter = null, string $direction = 'asc') : array
    {
        $collection = (array) $collection;

        // Get correct PHP constant for direction
        $directionNumber = (strtolower($direction) === 'desc') ? SORT_DESC : SORT_ASC;

        // Transform all values into their results
        if ($sorter) {
            $results = ArraysMethods::each(
                $collection,
                fn($value) => \is_callable($sorter) ? $sorter($value) : ArraysMethods::get($value, $sorter)
            );
        } else {
            $results = $collection;
        }

        // Sort by the results and replace by original values
        array_multisort($results, $directionNumber, SORT_REGULAR, $collection);

        return $collection;
    }

    /**
     * Group values from a collection according to the results of a closure.
     *
     * @param      $collection
     * @param      $grouper
     * @param  bool  $saveKeys
     *
     * @return array
     */
    public static function group(mixed $collection, callable|string $grouper, bool $saveKeys = false) : array
    {
        $collection = (array) $collection;
        $result     = [];

        // Iterate over values, group by property/results from closure
        foreach ($collection as $key => $value) {
            $groupKey = \is_callable($grouper) ? $grouper($value, $key) : ArraysMethods::get($value, $grouper);
            $newValue = static::get($result, $groupKey);

            // Add to results
            if ($groupKey !== null && $saveKeys) {
                $result[$groupKey] = $newValue;
                $result[$groupKey][$key] = $value;
            } elseif ($groupKey !== null) {
                $result[$groupKey] = $newValue;
                $result[$groupKey][] = $value;
            }
        }

        return $result;
    }

    ////////////////////////////////////////////////////////////////////
    ////////////////////////////// HELPERS /////////////////////////////
    ////////////////////////////////////////////////////////////////////

    /**
     * Internal mechanic of set method.
     *
     * @param $collection
     * @param $key
     * @param $value
     *
     * @return mixed
     */
    protected static function internalSet(&$collection, $key, $value) : mixed
    {
        if ($key === null) {
            return $collection = $value;
        }

        // Explode the keys
        $keys = explode('.', (string) $key);

        // Crawl through the keys
        while (\count($keys) > 1) {
            $key = array_shift($keys);

            // If we're dealing with an object
            if (\is_object($collection)) {
                $collection->{$key} = static::get($collection, $key, []);
                $collection         = &$collection->{$key};
                // If we're dealing with an array
            } else {
                $collection[$key] = static::get($collection, $key, []);
                $collection       = &$collection[$key];
            }
        }

        // Bind final tree on the collection
        $key = array_shift($keys);
        if (\is_array($collection)) {
            $collection[$key] = $value;
        } else {
            $collection->{$key} = $value;
        }

        return $collection;
    }

    /**
     * Internal mechanics of remove method.
     *
     * @param  array|object  $collection
     * @param  string  $key
     *
     * @return mixed
     */
    protected static function internalRemove(array|object &$collection, mixed $key) : bool
    {
        // Explode keys
        $keys = explode('.', $key);

        // Crawl though the keys
        while (\count($keys) > 1) {
            $key = array_shift($keys);

            if ( ! static::has($collection, $key)) {
                return false;
            }

            // If we're dealing with an object
            if (\is_object($collection)) {
                $collection = &$collection->{$key};
                // If we're dealing with an array
            } else {
                $collection = &$collection[$key];
            }
        }

        $key = array_shift($keys);
        if (\is_object($collection)) {
            unset($collection->{$key});
        } else {
            unset($collection[$key]);
        }

        return true;
    }

    /**
     * Given a list, and an iteratee function that returns
     * a key for each element in the list (or a property name),
     * returns an object with an index of each item.
     * Just like groupBy, but for when you know your keys are unique.
     */
    public static function indexBy(array $array, mixed $key) : array
    {
        $results = [];

        foreach ($array as $a) {
            if (isset($a[$key])) {
                $results[$a[$key]] = $a;
            }
        }

        return $results;
    }
}
