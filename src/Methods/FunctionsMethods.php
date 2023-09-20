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
use Exception;

/**
 * Methods to manage functions.
 */
class FunctionsMethods
{

    /**
     * An array of functions to be called X times.
     *
     * @var array
     */
    public static array $canBeCalledTimes = [];

    /**
     * An array of cached function results.
     *
     * @var array
     */
    public static array $cached = [];

    /**
     * An array tracking the last time a function was called.
     *
     * @var array
     */
    public static array $throttle = [];

    ////////////////////////////////////////////////////////////////////
    ////////////////////////////// LIMITERS ////////////////////////////
    ////////////////////////////////////////////////////////////////////

    /**
     * Create a function that can only be called once.
     *
     * @param  callable  $function  The function
     *
     * @return Closure
     * @throws Exception
     */
    public static function once(callable $function) : callable
    {
        return static::only($function, 1);
    }

    /**
     * Create a function that can only be called $times times.
     *
     * @param  callable  $function
     * @param  int  $canBeCalledTimes  $times    The number of times
     *
     * @return Closure
     * @throws Exception
     */
    public static function only(callable $function, int $canBeCalledTimes) : callable
    {
        $unique = random_int(0, mt_getrandmax());

        // Create a closure that check if the function was already called
        return function (...$arguments) use ($function, $canBeCalledTimes, $unique) {

            $signature = FunctionsMethods::getSignature((string) $unique, $function, $arguments);

            // Get counter
            $numberOfTimesCalled = FunctionsMethods::hasBeenCalledTimes($signature);

            // If the function has been called too many times, cancel
            // Else, increment the count
            if ($numberOfTimesCalled >= $canBeCalledTimes) {
                return false;
            }

            ++FunctionsMethods::$canBeCalledTimes[$signature];

            return \call_user_func_array($function, $arguments);
        };
    }

    /**
     * Create a function that can only be called after $times times.
     *
     * @param  callable  $function
     * @param  int  $times
     *
     * @return Closure
     * @throws Exception
     */
    public static function after(callable $function, int $times) : callable
    {
        $unique = random_int(0, mt_getrandmax());

        // Create a closure that check if the function was already called
        return function (...$arguments) use ($function, $times, $unique) {

            $signature = FunctionsMethods::getSignature((string) $unique, $function, $arguments);

            // Get counter
            $called = FunctionsMethods::hasBeenCalledTimes($signature);

            // Prevent calling before a certain number
            if ($called < $times) {
                ++FunctionsMethods::$canBeCalledTimes[$signature];

                return false;
            }

            return \call_user_func_array($function, $arguments);
        };
    }

    /**
     * Caches the result of a function and refer to it ever after.
     *
     * @param  callable  $function
     *
     * @return Closure
     * @throws Exception
     */
    public static function cache(callable $function) : callable
    {
        $unique = random_int(0, mt_getrandmax());

        return function (...$arguments) use ($function, $unique) {

            $signature = FunctionsMethods::getSignature((string) $unique, $function, $arguments);

            if (isset(FunctionsMethods::$cached[$signature])) {
                return FunctionsMethods::$cached[$signature];
            }

            $result                               = \call_user_func_array($function, $arguments);
            FunctionsMethods::$cached[$signature] = $result;

            return $result;
        };
    }

    /**
     * Only allow a function to be called every X ms.
     *
     * @param  callable  $function
     * @param  int  $ms
     *
     * @return Closure
     * @throws Exception
     */
    public static function throttle(callable $function, int $ms) : callable
    {
        $unique = random_int(0, mt_getrandmax());

        return function (...$arguments) use ($function, $ms, $unique) {

            $signature = FunctionsMethods::getSignature((string) $unique, $function, $arguments);

            // Check last called time and update it if necessary
            $last       = FunctionsMethods::getLastCalledTime($signature);
            $difference = time() - $last;

            // Execute the function if the conditions are here
            if ($last === time() || $difference > $ms) {
                FunctionsMethods::$throttle[$signature] = time();

                return \call_user_func_array($function, $arguments);
            }

            return false;
        };
    }

    /**
     * Prefill function arguments.
     *
     * @return Closure
     * @author Jeremy Ashkenas
     */
    public static function partial(callable $func) : callable
    {
        $boundArgs = \array_slice(\func_get_args(), 1);

        return function () use ($boundArgs, $func) {
            $args       = [];
            $calledArgs = \func_get_args();
            $position   = 0;

            foreach ($boundArgs as $i => $iValue) {
                $args[] = $iValue ?? $calledArgs[$position++];
            }

            return \call_user_func_array($func, array_merge($args, \array_slice($calledArgs, $position)));
        };
    }

    ////////////////////////////////////////////////////////////////////
    ////////////////////////////// HELPERS /////////////////////////////
    ////////////////////////////////////////////////////////////////////
    /**
     * Get the last time a function was called.
     *
     * @param  string  $unique  The function unique ID
     */
    public static function getLastCalledTime(string $unique) : int
    {
        return ArraysMethods::setAndGet(static::$canBeCalledTimes, $unique, time());
    }

    /**
     * Get the number of times a function has been called.
     *
     * @param  string  $unique  The function unique ID
     */
    public static function hasBeenCalledTimes(string $unique) : int
    {
        return ArraysMethods::setAndGet(static::$canBeCalledTimes, $unique, 0);
    }

    /**
     * Get a function's signature.
     *
     * @param         $unique
     * @param  Closure  $function  The function
     * @param  array  $arguments  Its arguments
     *
     * @return string The unique id
     */
    public static function getSignature(string $unique, Closure $function, array $arguments) : string
    {
        $function  = var_export($function, true);
        $argumentsStr = var_export($arguments, true);

        return md5($unique.'_'.$function.'_'.$argumentsStr);
    }
}
