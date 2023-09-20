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

use Underscore\Methods\ArraysMethods;
use Underscore\Traits\Repository;

/**
 * The base class and wrapper around all other classes.
 */
class Underscore extends Repository
{
    /**
     * The current config.
     *
     * @var array
     */
    protected static array $options = [];

    ////////////////////////////////////////////////////////////////////
    //////////////////////////// INTERFACE /////////////////////////////
    ////////////////////////////////////////////////////////////////////

    /**
     * Dispatch to the correct Repository class.
     *
     * @param mixed $subject The subject
     *
     * @return Repository
     */
    public static function from($subject) : Repository
    {
        $class = Dispatch::toClass($subject);

        return $class::from($subject);
    }

    ////////////////////////////////////////////////////////////////////
    ///////////////////////////// HELPERS //////////////////////////////
    ////////////////////////////////////////////////////////////////////

    /**
     * Get an option from the config file.
     *
     * @param  string  $option  The key of the option
     *
     * @return mixed Its value
     */
    public static function option(string $option) : mixed
    {
        // Get config file
        if (!static::$options) {
            static::$options = include __DIR__.'/../config/underscore.php';
        }

        return ArraysMethods::get(static::$options, $option);
    }
}
