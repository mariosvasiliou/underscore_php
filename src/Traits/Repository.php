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

namespace Underscore\Traits;

use BadMethodCallException;
use Closure;
use JsonException;
use RuntimeException;
use Underscore\Dispatch;
use Underscore\Method;
use Underscore\Methods\ArraysMethods;
use Underscore\Methods\StringsMethods;
use Underscore\Parse;

/**
 * Base abstract class for repositories.
 */
abstract class Repository
{
    /**
     * The subject of the repository.
     *
     * @var mixed
     */
    protected mixed $subject;

    /**
     * Custom functions.
     *
     * @var array
     */
    protected static array $macros = [];

    /**
     * The method used to convert new subjects.
     *
     * @var string
     */
    protected string $typecaster = '';

    ////////////////////////////////////////////////////////////////////
    /////////////////////////// PUBLIC METHODS /////////////////////////
    ////////////////////////////////////////////////////////////////////

    /**
     * Create a new instance of the repository.
     *
     * @param  mixed|null  $subject  The repository subject
     */
    public function __construct(mixed $subject = null)
    {
        // Assign subject
        $this->subject = $subject ?: $this->getDefault();

        // Convert it if necessary
        $typecaster = $this->typecaster;
        if ($typecaster) {
            $this->$typecaster();
        }
    }

    /**
     * Transform subject to Strings on toString.
     *
     * @return string
     * @throws JsonException
     */
    public function __toString() : string
    {
        return Parse::toString($this->subject);
    }

    /**
     * Create a new Repository.
     */
    public static function create() : Repository
    {
        return new static();
    }

    /**
     * Create a new Repository from a subject.
     *
     * @param $subject
     *
     * @return Repository
     */
    public static function from($subject) : Repository
    {
        return new static($subject);
    }

    /**
     * Get a key from the subject.
     *
     * @param $key
     *
     * @return mixed
     */
    public function __get($key)
    {
        return ArraysMethods::get($this->subject, $key);
    }

    /**
     * Set a value on the subject.
     *
     * @param $key
     * @param $value
     */
    public function __set($key, $value)
    {
        $this->subject = ArraysMethods::set($this->subject, $key, $value);
    }


    /**
     * Check if the subject is empty.
     *
     * @return bool
     */
    public function isEmpty() : bool
    {
        return empty($this->subject);
    }

    /**
     * Replace the Subject while maintaining chain.
     *
     * @param mixed $value
     *
     * @return Repository
     */
    public function setSubject(mixed $value) : Repository
    {
        $this->subject = $value;

        return $this;
    }

    /**
     * Get the subject from the object.
     *
     * @return mixed
     */
    public function obtain() : mixed
    {
        return $this->subject;
    }

    /**
     * Extend the class with a custom function.
     *
     * @param  string  $method  The macro's name
     * @param  Closure  $closure  The macro
     */
    public static function extend(string $method, Closure $closure) : void
    {
        static::$macros[static::class][$method] = $closure;
    }

    ////////////////////////////////////////////////////////////////////
    //////////////////////// METHODS DISPATCHING ///////////////////////
    ////////////////////////////////////////////////////////////////////

    /**
     * Catch aliases and reroute them to the right methods.
     *
     * @param $method
     * @param $parameters
     *
     * @return mixed
     */
    public static function __callStatic($method, $parameters)
    {
        // Get base class and methods class
        $callingClass = static::computeClassToCall(static::class, $method, $parameters);
        $methodsClass = Method::getMethodsFromType($callingClass);

        // Defer to Methods class
        if (method_exists($methodsClass, $method)) {
            return self::callMethod($methodsClass, $method, $parameters);
        }

        // Check for an alias
        $alias = Method::getAliasOf($method);
        if ($alias) {
            return self::callMethod($methodsClass, $alias, $parameters);
        }

        // Check for parsers
        if (method_exists(Parse::class, $method)) {
            return self::callMethod(Parse::class, $method, $parameters);
        }

        // Defered methods
        $defered = Dispatch::toNative($callingClass, $method);
        if ($defered) {
            return \call_user_func_array($defered, $parameters);
        }

        // Look in the macros
        $macro = ArraysMethods::get(static::$macros, $callingClass.'.'.$method);
        if ($macro) {
            return \call_user_func_array($macro, $parameters);
        }

        throw new BadMethodCallException('The method '.$callingClass.'::'.$method.' does not exist');
    }

    /**
     * Allow the chained calling of methods.
     *
     * @param $method
     * @param $arguments
     *
     * @return Repository
     */
    public function __call($method, $arguments)
    {
        // Get correct class
        $class = Dispatch::toClass($this->subject);

        // Check for unchainable methods
        if (Method::isUnchainable($class, $method)) {
            throw new BadMethodCallException('The method '.$class.'::'.$method.' can\'t be chained');
        }

        // Prepend subject to arguments and call the method
        if ( ! Method::isSubjectless($method)) {
            array_unshift($arguments, $this->subject);
        }
        $result = $class::__callStatic($method, $arguments);

        // If the method is a breaker, return just the result
        if (Method::isBreaker($method)) {
            return $result;
        }

        $this->subject = $result;

        return $this;
    }

    ////////////////////////////////////////////////////////////////////
    ///////////////////////////// HELPERS //////////////////////////////
    ////////////////////////////////////////////////////////////////////

    /**
     * Tries to find the right class to call.
     *
     * @param  string  $callingClass  The original class
     * @param  string  $method  The method
     * @param array  $arguments    The arguments
     *
     * @return string The correct class
     */
    protected static function computeClassToCall(string $callingClass, string $method, ...$arguments) : string
    {
        if ( ! StringsMethods::find($callingClass, 'Underscore\Types')) {
            if (isset($arguments[0])) {
                $callingClass = Dispatch::toClass($arguments[0]);
            } else {
                $callingClass = Method::findInClasses($callingClass, $method);
            }
        }

        return $callingClass;
    }

    /**
     * Simpler version of call_user_func_array (for performances).
     *
     * @param string $class      The class
     * @param string $method     The method
     * @param array  $parameters The arguments
     *
     * @return mixed
     */
    protected static function callMethod(string $class, string $method, array $parameters) : mixed
    {
        return match (\count($parameters)) {
            0 => $class::$method(),
            1 => $class::$method($parameters[0]),
            2 => $class::$method($parameters[0], $parameters[1]),
            3 => $class::$method($parameters[0], $parameters[1], $parameters[2]),
            4 => $class::$method($parameters[0], $parameters[1], $parameters[2], $parameters[3]),
            5 => $class::$method($parameters[0], $parameters[1], $parameters[2], $parameters[3], $parameters[4]),
            default => throw new RuntimeException('No appropriate method found'),
        };
    }

    /**
     * Get a default value for a new repository.
     *
     * @return mixed
     */
    protected function getDefault() : mixed
    {
        return '';
    }
}
