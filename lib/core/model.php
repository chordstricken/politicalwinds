<?php

namespace core;

use \Exception;

/**
 * @author Jason Wright <jason@silvermast.io>
 * @since 1/4/17
 * @package prunejuice
 */
abstract class Model {
    use Singleton;

    /** @var mixed */
    protected static $_indexes;

    public $date_added;
    public $date_modified;

    /**
     * Base constructor.
     * @param array $vars
     */
    public function __construct($vars = []) {
        $this->setVars($vars);
    }

    /**
     * @param array $vars
     * @return $this
     */
    public function setVars($vars = []) {
        if (empty($vars))
            return $this;

        if (is_object($vars))
            $vars = get_object_vars($vars);

        foreach ($vars as $key => $val)
            if (property_exists($this, $key)) $this->$key = $val;

        return $this;
    }

    /**
     * @throws Exception
     * @return self
     */
    abstract public function validate();

    /**
     * Saves the object
     * @throws Exception
     * @return self
     */
    abstract public function save();

    /**
     * Updates only specified fields on an object
     * @param array $set
     * @return static
     * @throws Exception
     */
    abstract public function update($set);

    /**
     * Deletes this object from the database
     * @return self
     */
    abstract public function delete();

    /**
     * Simple web-safe random ID string generator
     * @return string
     */
    public static function generateId() {
        $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $len   = strlen($chars) - 1;
        $id    = '';
        for ($i = 0; $i < 16; $i++)
            $id .= $chars[mt_rand(0, $len)];

        return $id;
    }

    /**
     * Returns the number of objects matching the query
     * @param $query
     * @return int|false
     */
    abstract public static function count($query = []);

    /**
     * Finds a single object
     * @param $query
     * @return static|null
     */
    abstract public static function findOne($query, $queryOptions = []);

    /**
     * Returns an array of objects (in memory)
     * @param $query
     * @param array $queryOptions
     * @return static[]
     */
    abstract public static function findMulti($query, $queryOptions = []);

    /**
     * Deletes objects from the table
     * @param $query
     * @return int|false
     * @throws Exception
     */
    abstract public static function deleteMulti(array $query);

    /**
     * Inserts an array of objects
     * @param $set
     * @param bool $replace
     * @return bool
     */
    abstract public static function insertMulti($set, $replace = false);

    /**
     * Returns an array of objects (in memory)
     * @param $query
     * @param $set
     * @return bool
     */
    abstract public static function updateMulti($query, $set);
}