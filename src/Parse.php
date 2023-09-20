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

use JsonException;
use Underscore\Methods\ArraysMethods;

/**
 * Parse from various formats to various formats.
 */
class Parse
{
    ////////////////////////////////////////////////////////////////////
    /////////////////////////////// JSON ///////////////////////////////
    ////////////////////////////////////////////////////////////////////

    /**
     * Converts data from JSON.
     *
     * @param  string  $data  The data to parse
     *
     * @return mixed
     * @throws JsonException
     */
    public static function fromJSON(string $data) : mixed
    {
        return json_decode($data, true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * Converts data to JSON.
     *
     * @param  mixed  $data  The data to convert
     *
     * @return string Converted data
     * @throws JsonException
     */
    public static function toJSON(mixed $data) : string
    {
        return json_encode($data, JSON_THROW_ON_ERROR);
    }

    ////////////////////////////////////////////////////////////////////
    //////////////////////////////// XML ///////////////////////////////
    ////////////////////////////////////////////////////////////////////

    /**
     * Converts data from XML.
     *
     * @param  string  $xml  The data to parse
     *
     * @return array
     * @throws JsonException
     */
    public static function fromXML(string $xml) : array
    {
        $xmlEl  = simplexml_load_string($xml);
        $xmlEnc = json_encode($xmlEl, JSON_THROW_ON_ERROR);

        return json_decode($xmlEnc, true, 512, JSON_THROW_ON_ERROR);
    }

    ////////////////////////////////////////////////////////////////////
    //////////////////////////////// CSV ///////////////////////////////
    ////////////////////////////////////////////////////////////////////

    /**
     * Converts data from CSV.
     *
     * @param  string  $data  The data to parse
     * @param  bool  $hasHeaders  Whether the CSV has headers
     *
     * @return mixed
     */
    public static function fromCSV(string $data, bool $hasHeaders = false) : array
    {
        $data = trim($data);

        // Explodes rows
        $dataArray = static::explodeWith($data, [PHP_EOL, "\r", "\n"]);
        $dataArray = array_map(function ($row) {
            return Parse::explodeWith($row, [';', "\t", ',']);
        }, $dataArray);

        // Get headers
        $headers = $hasHeaders ? $dataArray[0] : array_keys($dataArray[0]);
        if ($hasHeaders) {
            array_shift($dataArray);
        }

        // Parse the columns in each row
        $array = [];
        foreach ($dataArray as $row => $columns) {
            foreach ($columns as $columnNumber => $column) {
                $array[$row][$headers[$columnNumber]] = $column;
            }
        }

        return $array;
    }

    /**
     * Converts data to CSV.
     *
     * @param mixed  $data          The data to convert
     * @param  string  $delimiter
     * @param  bool  $exportHeaders
     *
     * @return string Converted data
     */
    public static function toCSV(mixed $data, string $delimiter = ';', bool $exportHeaders = false) : string
    {
        $csv = [];

        // Convert objects to arrays
        if (\is_object($data)) {
            $data = (array) $data;
        }

        // Don't convert if it's not an array
        if ( ! \is_array($data)) {
            return $data;
        }

        // Fetch headers if requested
        if ($exportHeaders) {
            $headers = array_keys(ArraysMethods::first($data));
            $csv[] = implode($delimiter, $headers);
        }

        // Quote values and create row
        foreach ($data as $header => $row) {
            // If single column
            if ( ! \is_array($row)) {
                $csv[] = '"'.$header.'"'.$delimiter.'"'.$row.'"';
                continue;
            }

            // Else add values
            foreach ($row as $key => $value) {
                $row[$key] = '"'.stripslashes($value).'"';
            }

            $csv[] = implode($delimiter, $row);
        }

        return implode(PHP_EOL, $csv);
    }

    ////////////////////////////////////////////////////////////////////
    ///////////////////////// TYPES SWITCHERS //////////////////////////
    ////////////////////////////////////////////////////////////////////

    /**
     * Converts data to an array.
     *
     * @param  mixed  $data
     *
     * @return array
     */
    public static function toArray(mixed $data) : array
    {
        // Look for common array conversion patterns in objects
        if (\is_object($data) && method_exists($data, 'toArray')) {
            $data = $data->toArray();
        }

        return (array) $data;
    }

    /**
     * Converts data to a string.
     *
     * @param  mixed  $data
     *
     * @return string
     * @throws JsonException
     */
    public static function toString(mixed $data) : string
    {
        // Avoid Array to Strings conversion exception
        if (\is_array($data)) {
            return static::toJSON($data);
        }

        return (string) $data;
    }

    /**
     * Converts data to an integer.
     *
     * @param  mixed  $data
     *
     * @return int
     */
    public static function toInteger(mixed $data) : int
    {
        // Returns size of arrays
        if (\is_array($data)) {
            return \count($data);
        }

        // Returns size of strings
        if (\is_string($data) && ! preg_match('/[0-9. ,]+/', $data)) {
            return \strlen($data);
        }

        return (int) $data;
    }

    /**
     * Converts data to a boolean.
     *
     * @param  mixed  $data
     *
     * @return bool
     */
    public static function toBoolean(mixed $data) : bool
    {
        return (bool) $data;
    }

    /**
     * Converts data to an object.
     *
     * @param  mixed  $data
     *
     * @return object
     */
    public static function toObject(mixed $data) : object
    {
        return (object) $data;
    }

    ////////////////////////////////////////////////////////////////////
    ///////////////////////////// HELPERS //////////////////////////////
    ////////////////////////////////////////////////////////////////////

    /**
     * Tries to explode a string with an array of delimiters.
     *
     * @param  string  $string  The string
     * @param array  $delimiters An array of delimiters
     *
     * @return array
     */
    public static function explodeWith(string $string, array $delimiters) : array
    {
        $array = $string;

        foreach ($delimiters as $delimiter) {
            $array = explode($delimiter, $string);
            if (\count($array) === 1) {
                continue;
            }

            return $array;
        }

        return $array;
    }
}
