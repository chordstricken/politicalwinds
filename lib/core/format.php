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

}