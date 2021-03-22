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

use Doctrine\Inflector\CachedWordInflector;
use Doctrine\Inflector\Inflector;
use Doctrine\Inflector\Rules\English\Rules;
use Doctrine\Inflector\RulesetInflector;
use RuntimeException;
use Underscore\Types\Strings;
use function Symfony\Component\String\u;

/**
 * Methods to manage strings.
 */
class StringsMethods
{

    /**
     * Uncountable word forms.
     *
     * @var array
     */
    public static $uncountable = [
        'audio',
        'bison',
        'cattle',
        'chassis',
        'compensation',
        'coreopsis',
        'data',
        'deer',
        'education',
        'emoji',
        'equipment',
        'evidence',
        'feedback',
        'firmware',
        'fish',
        'furniture',
        'gold',
        'hardware',
        'information',
        'jedi',
        'kin',
        'knowledge',
        'love',
        'metadata',
        'money',
        'moose',
        'news',
        'nutrition',
        'offspring',
        'plankton',
        'pokemon',
        'police',
        'rain',
        'recommended',
        'related',
        'rice',
        'series',
        'sheep',
        'software',
        'species',
        'swine',
        'traffic',
        'wheat',
    ];
    ////////////////////////////////////////////////////////////////////
    ////////////////////////////// CREATE  /////////////////////////////
    ////////////////////////////////////////////////////////////////////

    /**
     * Create a string from a number.
     *
     * @param int    $count A number
     * @param string $many  If many
     * @param string $one   If one
     * @param string $zero  If one
     *
     * @return string A string
     */
    public static function accord($count, $many, $one, $zero = null) : string
    {
        if ($count === 1) {
            $output = $one;
        }
        elseif ($count === 0 && ! empty($zero)) {
            $output = $zero;
        }
        else {
            $output = $many;
        }

        return sprintf($output, $count);
    }

    /**
     * Generate a more truly "random" alpha-numeric string.
     *
     * @param int $length
     *
     * @throws \RuntimeException
     *
     * @return string
     *
     * @author Taylor Otwell
     */
    public static function random($length = 16) : string
    {
        if (\function_exists('openssl_random_pseudo_bytes')) {
            $bytes = openssl_random_pseudo_bytes($length * 2);

            if ($bytes === false) {
                throw new RuntimeException('Unable to generate random string.');
            }

            return substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $length);
        }

        return static::quickRandom($length);
    }

    /**
     * Generate a "random" alpha-numeric string.
     *
     * Should not be considered sufficient for cryptography, etc.
     *
     * @param int $length
     *
     * @return string
     *
     * @author Taylor Otwell
     */
    public static function quickRandom($length = 16) : string
    {
        $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        return substr(str_shuffle(str_repeat($pool, $length)), 0, $length);
    }

    /**
     * Generates a random suite of words.
     *
     * @param int $words  The number of words
     * @param int $length The length of each word
     *
     * @return string
     */
    public static function randomStrings($words, $length = 10) : string
    {
        return Strings::from('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
                      ->shuffle()
                      ->split($length)
                      ->slice(0, $words)
                      ->implode(' ')
                      ->obtain();
    }

    ////////////////////////////////////////////////////////////////////
    ////////////////////////////// ANALYZE /////////////////////////////
    ////////////////////////////////////////////////////////////////////

    /**
     * Determine if a given string ends with a given substring.
     *
     * @param string       $haystack
     * @param string|array $needles
     *
     * @return bool
     *
     * @author Taylor Otwell
     */
    public static function endsWith($haystack, $needles) : bool
    {
        foreach ((array)$needles as $needle) {
            if ((string)$needle === substr($haystack, -\strlen($needle))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if a string is an IP.
     *
     * @param $string
     *
     * @return bool
     */
    public static function isIp($string) : bool
    {
        return filter_var($string, FILTER_VALIDATE_IP) !== false;
    }

    /**
     * Check if a string is an email.
     *
     * @param $string
     *
     * @return bool
     */
    public static function isEmail($string) : bool
    {
        return filter_var($string, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Check if a string is an url.
     *
     * @param $string
     *
     * @return bool
     */
    public static function isUrl($string) : bool
    {
        return filter_var($string, FILTER_VALIDATE_URL) !== false;
    }

    /**
     * Determine if a given string starts with a given substring.
     *
     * @param string       $haystack
     * @param string|array $needles
     *
     * @return bool
     *
     * @author Taylor Otwell
     */
    public static function startsWith($haystack, $needles) : bool
    {
        foreach ((array)$needles as $needle) {
            if ($needle !== '' && strpos($haystack, $needle) === 0) {
                return true;
            }
        }

        return false;
    }

    ////////////////////////////////////////////////////////////////////
    ///////////////////////////// FETCH FROM ///////////////////////////
    ////////////////////////////////////////////////////////////////////

    /**
     * Find one or more needles in one or more haystacks.
     *
     * @param array|string $string        The haystack(s) to search in
     * @param array|string $needle        The needle(s) to search for
     * @param bool         $caseSensitive Whether the function is case sensitive or not
     * @param bool         $absolute      Whether all needle need to be found or whether one is enough
     *
     * @return bool Found or not
     */
    public static function find($string, $needle, $caseSensitive = false, $absolute = false) : bool
    {
        // If several needles
        if (\is_array($needle) or \is_array($string)) {
            $sliceFrom = $string;
            $sliceTo   = $needle;

            if (\is_array($needle)) {
                $sliceFrom = $needle;
                $sliceTo   = $string;
            }

            $found = 0;
            foreach ($sliceFrom as $need) {
                if (static::find($sliceTo, $need, $absolute, $caseSensitive)) {
                    ++$found;
                }
            }

            return ($absolute) ? \count($sliceFrom) === $found : $found > 0;
        }

        // If not case sensitive
        if (!$caseSensitive) {
            $string = strtolower($string);
            $needle = strtolower($needle);
        }

        // If string found
        $pos = strpos($string, $needle);

        return !($pos === false);
    }

    /**
     * Slice a string with another string.
     *
     * @param $string
     * @param $slice
     *
     * @return array
     */
    public static function slice($string, $slice) : array
    {
        $sliceTo   = static::sliceTo($string, $slice);
        $sliceFrom = static::sliceFrom($string, $slice);

        return [$sliceTo, $sliceFrom];
    }

    /**
     * Slice a string from a certain point.
     *
     * @param $string
     * @param $slice
     *
     * @return false|string
     */
    public static function sliceFrom($string, $slice)
    {
        $slice = strpos($string, $slice);

        return substr($string, $slice);
    }

    /**
     * Slice a string up to a certain point.
     *
     * @param $string
     * @param $slice
     *
     * @return false|string
     */
    public static function sliceTo($string, $slice)
    {
        $slice = strpos($string, $slice);

        return substr($string, 0, $slice);
    }

    /**
     * Get the base class in a namespace.
     *
     * @param string $string
     *
     * @return string
     */
    public static function baseClass($string) : string
    {
        $string = static::replace($string, '\\', '/');

        return basename($string);
    }

    ////////////////////////////////////////////////////////////////////
    /////////////////////////////// ALTER //////////////////////////////
    ////////////////////////////////////////////////////////////////////

    /**
     * Prepend a string with another.
     *
     * @param string $string The string
     * @param string $with   What to prepend with
     *
     * @return string
     */
    public static function prepend($string, $with) : string
    {
        return $with.$string;
    }

    /**
     * Append a string to another.
     *
     * @param string $string The string
     * @param string $with   What to append with
     *
     * @return string
     */
    public static function append($string, $with) : string
    {
        return $string.$with;
    }

    /**
     * Limit the number of characters in a string.
     *
     * @param string $value
     * @param int    $limit
     * @param string $end
     *
     * @return string
     *
     * @author Taylor Otwell
     */
    public static function limit($value, $limit = 100, $end = '...') : string
    {
        if (mb_strlen($value) <= $limit) {
            return $value;
        }

        return rtrim(mb_substr($value, 0, $limit, 'UTF-8')).$end;
    }

    /**
     * Remove part of a string.
     *
     * @param $string
     * @param $remove
     *
     * @return string
     */
    public static function remove($string, $remove) : string
    {
        if (\is_array($remove)) {
            $string = preg_replace('#('.implode('|', $remove).')#', null, $string);
        }

        // Trim and return
        return trim(str_replace($remove, null, $string));
    }

    /**
     * Correct arguments order for str_replace.
     *
     * @param $string
     * @param $replace
     * @param $with
     *
     * @return string|string[]
     */
    public static function replace($string, $replace, $with)
    {
        return str_replace($replace, $with, $string);
    }

    /**
     * Toggles a string between two states.
     *
     * @param string $string The string to toggle
     * @param string $first  First value
     * @param string $second Second value
     * @param bool   $loose  Whether a string neither matching 1 or 2 should be changed
     *
     * @return string The toggled string
     */
    public static function toggle($string, $first, $second, $loose = false) : string
    {
        // If the string given match none of the other two, and we're in strict mode, return it
        if ( ! $loose and ! \in_array($string, [$first, $second], true)) {
            return $string;
        }

        return $string === $first ? $second : $first;
    }

    /**
     * Generate a URL friendly "slug" from a given string.
     *
     * @param string $title
     * @param string $separator
     *
     * @return string
     *
     * @author Taylor Otwell
     */
    protected static function slug($title, $separator = '-') : string
    {
        $title = u($title)->ascii()->toString();
        // Convert all dashes/underscores into separator
        $flip = $separator === '-' ? '_' : '-';

        $title = preg_replace('!['.preg_quote($flip).']+!u', $separator, $title);

        // Remove all characters that are not the separator, letters, numbers, or whitespace.
        $title = preg_replace('![^'.preg_quote($separator).'\pL\pN\s]+!u', '', mb_strtolower($title));

        // Replace all separator characters and whitespace by a single separator
        $title = preg_replace('!['.preg_quote($separator).'\s]+!u', $separator, $title);

        return trim($title, $separator);
    }

    /**
     * Slugifies a string.
     *
     * @param        $string
     * @param string $separator
     *
     * @return string
     */
    public static function slugify($string, $separator = '-') : string
    {
        $string = str_replace('_', ' ', $string);

        return static::slug($string, $separator);
    }

    /**
     * Explode a string into an array.
     *
     * @param      $string
     * @param      $with
     * @param null $limit
     *
     * @return array
     */
    public static function explode($string, $with, $limit = null) : array
    {
        if ( ! $limit) {
            return explode($with, $string);
        }

        return explode($with, $string, $limit);
    }

    /**
     * Lowercase a string.
     *
     * @param string $string
     *
     * @return string
     */
    public static function lower($string) : string
    {
        return mb_strtolower($string, 'UTF-8');
    }

    /**
     * Get the plural form of an English word.
     *
     * @param string $value
     * @param int    $count
     *
     * @return string
     */
    public static function plural($value)
    {
        if (static::uncountable($value)) {
            return $value;
        }

        $plural = static::inflector()->pluralize($value);

        return static::matchCase($plural, $value);
    }

    /**
     * Get the singular form of an English word.
     *
     * @param string $value
     * @return string
     */
    public static function singular($value)
    {
        $singular = static::inflector()->singularize($value);

        return static::matchCase($singular, $value);
    }

    /**
     * Lowercase a string.
     *
     * @param string $string
     *
     * @return string
     */
    public static function upper($string) : string
    {
        return mb_strtoupper($string, 'UTF-8');
    }

    /**
     * Convert a string to title case.
     *
     * @param string $string
     *
     * @return string
     */
    public static function title($string) : string
    {
        return mb_convert_case($string, MB_CASE_TITLE, 'UTF-8');
    }

    /**
     * Limit the number of words in a string.
     *
     * @param string $value
     * @param int    $words
     * @param string $end
     *
     * @return string
     *
     * @author Taylor Otwell
     */
    public static function words($value, $words = 100, $end = '...') : string
    {
        preg_match('/^\s*+(?:\S++\s*+){1,'.$words.'}/u', $value, $matches);

        if ( ! isset($matches[0]) || \strlen($value) === \strlen($matches[0])) {
            return $value;
        }

        return rtrim($matches[0]).$end;
    }

    ////////////////////////////////////////////////////////////////////
    /////////////////////////// CASE SWITCHERS /////////////////////////
    ////////////////////////////////////////////////////////////////////

    /**
     * Convert a string to PascalCase.
     *
     * @param string $string
     *
     * @return string
     */
    public static function toPascalCase($string) : string
    {
        return u($string)->camel()->title()->toString();
    }

    /**
     * Convert a string to snake_case.
     *
     * @param string $string
     *
     * @return string
     */
    public static function toSnakeCase($string) : string
    {
        return u($string)->snake()->toString();
    }

    /**
     * Convert a string to camelCase.
     *
     * @param string $string
     *
     * @return string
     */
    public static function toCamelCase($string) : string
    {
        return u($string)->camel()->toString();
    }

    /**
     * Get the inflector instance.
     *
     * @return \Doctrine\Inflector\Inflector
     */
    public static function inflector()
    {
        static $inflector;

        if (is_null($inflector)) {
            $inflector = new Inflector(
                new CachedWordInflector(new RulesetInflector(
                    Rules::getSingularRuleset()
                )),
                new CachedWordInflector(new RulesetInflector(
                    Rules::getPluralRuleset()
                ))
            );
        }

        return $inflector;
    }

    /**
     * Determine if the given value is uncountable.
     *
     * @param string $value
     *
     * @return bool
     */
    protected static function uncountable($value) : bool
    {
        return \in_array(strtolower($value), static::$uncountable, true);
    }

    /**
     * Attempt to match the case on two strings.
     *
     * @param string $value
     * @param string $comparison
     *
     * @return string
     */
    protected static function matchCase($value, $comparison) : string
    {
        $functions = ['mb_strtolower', 'mb_strtoupper', 'ucfirst', 'ucwords'];

        foreach ($functions as $function) {
            if ($function($comparison) === $comparison) {
                return $function($value);
            }
        }

        return $value;
    }

}
