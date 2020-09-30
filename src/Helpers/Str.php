<?php
namespace Jtolj\HtmlAttributes\Helpers;

class Str
{

    /**
     * Determine if a given string contains a given substring.
     *
     * @param  string  $haystack
     * @param  string  $needle
     * @return bool
     */
    public static function endsWith(string $haystack, string $needle) : bool
    {

        if ($needle != '' && strpos($haystack, $needle) !== false) {
            return true;
        }

        return false;
    }

     /**
     * Extract the suffix from a string, given an array of suffixes to look for.
     *
     * @param  string  $haystack
     * @param  iterable  $suffixes
     * @return string|void;
     */
    public static function extractSuffix(string $haystack, iterable $suffixes)
    {
        foreach ($suffixes as $suffix) {
            if (self::endsWith($haystack, $suffix)) {
                return $suffix;
            }
        }
    }
}
