<?php
/**
 *
 * @author Jason Wright <jason.dee.wright@gmail.com>
 * @since 8/18/17
 * @package politics
 *
 */

namespace core\db\traits;

use core\Debug;
use core\db;
use \Exception;
use \mysqli;

/**
 * Trait Mysql
 * @package core\db
 * @property string $path
 */
trait Mysql {

    /** @var \mysqli_result[] */
    protected static $queryResults = [];

    /**
     * Inserts/updates the database object
     * @return bool
     */
    public function save() {
        try {
            $table  = static::TABLE;
            $array  = array_replace((array)self::new(), (array)$this); // Ignore fields not defined in class by default
            $fields = '`' . implode('`,`', array_keys($array)) . '`';
            $values = implode(',', array_map('db\Mysql::escapeWithQuotes', $array));
            $sql    = "INSERT INTO `$table` ($fields) VALUES ($values) ON DUPLICATE KEY UPDATE " . db\Mysql::buildFilter($this);

            if (!$result = db\Mysql::conn()->query($sql))
                throw new Exception(__METHOD__ . " Failed. SQL: $sql");

            if (!$this->{static::INDEX})
                $this->{static::INDEX} = db\Mysql::conn()->insert_id;

            return true;

        } catch (Exception $e) {
            Debug::error($e);
            return false;
        }
    }

    /**
     * Update only specific fields
     * Note: If updating the entire document, use ->save() instead.
     * @param array $set
     * @return bool
     */
    public function update($set) {
        try {
            if (!isset($this->{static::INDEX}))
                throw new Exception(__METHOD__ . " ID not set");

            return self::updateMulti([static::INDEX => $this->{static::INDEX}], $set);

        } catch (Exception $e) {
            Debug::error($e);
            return false;
        }
    }

    /**
     * Deletes the object
     * @return bool
     */
    public function delete() {
        if (!isset($this->{static::INDEX}))
            return true;

        return self::deleteMulti([static::INDEX => $this->{static::INDEX}]);
    }

    /**
     * Counts the documents in the table
     * @param array $query
     * @return bool|int
     */
    public static function count($query = []) {
        try {
            $query  = db\Mysql::buildFilter((array)$query);
            $table  = static::TABLE;
            $sql    = "SELECT COUNT(*) FROM `$table` WHERE $query";

            if (!$result = db\Mysql::conn()->query($sql))
                throw new Exception(__METHOD__ . " Failed. SQL: $sql");

            return $result->fetch_array(MYSQLI_NUM)[0];

        } catch (Exception $e) {
            Debug::error($e);
            return false;
        }
    }

    /**
     * Returns one object according to the query
     * @param $query
     * @return object|null
     */
    public static function findOne($query) {
        try {
            $query  = db\Mysql::buildFilter((array)$query);
            $table  = static::TABLE;
            $sql    = "SELECT * FROM `$table` WHERE $query LIMIT 1";

            if (!$result = db\Mysql::conn()->query($sql))
                throw new Exception(__METHOD__ . " Failed. SQL: $sql");

            $model = $result->fetch_object(get_called_class());
            $result->free_result();
            return $model;

        } catch (Exception $e) {
            Debug::error($e);
            return null;
        }
    }

    /**
     * Queries the database for the specified objects and returns one at a time
     *
     * @example while ($obj = obj::findMulti([])) { ... }
     *
     * @param $query
     * @param array $opts (limit, offset, sort)
     *  uses associative array for sort. -1 for DESC and 1 for ASC. example: ['foo' => -1, 'bar' => 1]
     * @return bool|object
     */
    public static function findMulti($query, $opts = []) {
        $resultId = md5(json_encode(func_get_args()));

        // Create a new result set if one does not exist
        if (!isset(self::$queryResults[$resultId])) {

            // result cursor is not set. Create a new one
            try {
                $query  = db\Mysql::buildFilter((array)$query);
                $table  = static::TABLE;
                $limit  = '';
                $sort   = '';

                if (isset($opts['limit']) && is_numeric($limit)) {
                    $limit = intval($limit);

                    if (isset($opts['offset']) && is_numeric($opts['offset']))
                        $limit = intval($opts['offset']) . ",$limit";

                    $limit = "LIMIT $limit";
                }

                if (isset($opts['sort']))
                    $sort = 'ORDER BY ' . db\Mysql::buildSort($opts['sort']);

                $sql = rtrim("SELECT * FROM $table WHERE $query $sort $limit");

                if (!$result = db\Mysql::conn()->query($sql))
                    throw new Exception(__METHOD__ . " Failed. SQL: $sql");

            } catch (Exception $e) {
                Debug::error($e);
                return false;
            }

        }

        // if that was the last row, clear the results and unset.
        if (!$currObj = self::$queryResults[$resultId]->fetch_object(get_called_class())) {
            self::$queryResults[$resultId]->free_result();
            unset(self::$queryResults[$resultId]);
        }

        return $currObj;
    }

    /**
     * Updates the table according to the query
     * @param $query
     * @param $set
     * @return bool
     */
    public static function updateMulti($query, $set) {
        try {
            $set    = db\Mysql::buildFilter((array)$set);
            $query  = db\Mysql::buildFilter((array)$query);
            $table  = static::TABLE;
            $sql    = "UPDATE `$table` SET $set WHERE $query";

            if (!$result = db\Mysql::conn()->query($sql))
                throw new Exception(__METHOD__ . " Failed. SQL: $sql");

            return true;

        } catch (Exception $e) {
            Debug::error($e);
            return false;
        }
    }

    /**
     * Deletes according to a query
     * @param array $query
     * @return bool
     */
    public static function deleteMulti(array $query) {
        try {
            $query  = db\Mysql::buildFilter((array)$query);
            $table  = static::TABLE;
            $sql    = "DELETE FROM `$table` WHERE $query";

            if (!$result = db\Mysql::conn()->query($sql))
                throw new Exception(__METHOD__ . " Failed. SQL: $sql");

            return true;

        } catch (Exception $e) {
            Debug::error($e);
            return false;
        }
    }

}