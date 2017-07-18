<?php

namespace core;

/**
 * This class is nearly an exact clone of the PECL http\Url class.
 * It exists because documentation/support of that class is sparse.
 * @author Jason Wright <jason.dee.wright@gmail.com>
 * @since 7/13/17
 * @package prunejuice
 */
class URL extends \http\Url {

    public $scheme;
    public $user;
    public $pass;
    public $host;
    public $port;
    public $path;
    public $query;
    public $fragment;

    /**
     * @param $param
     * @return URL
     */
    public static function new($param) {
        return new self($param);
    }

    /**
     * @param $params
     * @return string
     */
    public function stringify($params = null) {
        if (empty($params))
            $params = get_class_vars(get_class());

        $params = is_scalar($params) ? [$params] : (array)$params;
        $params = array_fill_keys($params, null); // create associative array from keys
        $params = array_intersect_key(get_object_vars($this), $params); // clone $this params into assoc array
        return self::new('')->mod($params)->__toString();
    }

}