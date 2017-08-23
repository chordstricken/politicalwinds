<?php

namespace core;

/**
 *
 * @author Jason Wright <jason.dee.wright@gmail.com>
 * @since 7/11/17
 * @package prunejuice
 */
class Format {

    /**
     * @param $path
     * @return mixed
     */
    public static function domain($path) {
        $path = trim($path);
        if (!preg_match('#^(\w+:)?//#', $path))
            return explode('/', $path, 2)[0];
        else
            return parse_url($path, PHP_URL_HOST);
    }

    /**
     * Returns the number with its ordinal suffix
     * 13 -> 13th
     * @param $num
     * @return string
     */
    public static function ordSuffix($num) {
        $ends = ['th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th'];
        if ((($num % 100) >= 11) && (($num % 100) <= 13))
            return $num . 'th';
        else
            return $num . $ends[$num % 10];
    }

}