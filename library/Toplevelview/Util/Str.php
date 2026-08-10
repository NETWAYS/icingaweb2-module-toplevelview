<?php

namespace Icinga\Module\Toplevelview\Util;

/**
 * Str is a small helper class for working with strings
 */
class Str
{
    /**
     * Str::limit method truncates the given string to the specified length.
     * Used in cases where CSS text-overflow cannot be used.
     *
     * @param   ?string $str
     * @param   int    $len
     * @param   string $end
     *
     * @return  string
     */
    public static function limit(?string $str, int $len = 25, string $end = '...'): string
    {
        if (empty($str)) {
            return '';
        }

       // If the string is smaller or equal to the limit we simply return it
        if (mb_strwidth($str, 'UTF-8') <= $len) {
            return $str;
        }

        // If the string is longer than the limit we truncate it
        // and add the given end to it.
        return mb_strimwidth($str, 0, $len, '', 'UTF-8') . $end;
    }

    /**
     * Transforms the title badge title "warning_unhandled" to "Warning Unhandled"
     *
     * @param   string $identifier
     *
     * @return  string
     */
    public static function prettyTitle(string $identifier): string
    {
        $s = '';
        foreach (explode('_', $identifier) as $p) {
            $s .= ' ' . ucfirst($p);
        }
        return trim($s);
    }
}
