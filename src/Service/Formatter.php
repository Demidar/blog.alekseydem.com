<?php

namespace App\Service;

class Formatter
{
    /**
     * @param string|string[] $stringOrArray
     * @return string|string[]
     */
    public static function humanize($stringOrArray)
    {
        if (is_array($stringOrArray)) {
            return array_map(static function($string) {return self::humanizeString($string);}, $stringOrArray);
        }
        return self::humanizeString($stringOrArray);
    }

    private static function humanizeString(string $string)
    {
        $result = preg_replace_callback('/(^\w)|([[:upper:]])|(\d+)/u', static function ($matches) {
            // if it's a first letter, make it uppercase
            if (isset($matches[1]) && !empty($matches[1])) {
                return mb_strtoupper($matches[1]);
            }
            // if it's a uppercase letter, make it lowercase and add a space before
            if (isset($matches[2]) && !empty($matches[2])) {
                return ' ' . mb_strtolower($matches[2]);
            }
            // if it's a number, add spaces between
            if (isset($matches[3]) && !empty($matches[3])) {
                return ' ' . $matches[3] . ' ';
            }
        }, $string);

        return trim(preg_replace('/\s+/', ' ', $result));
    }
}
