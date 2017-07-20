<?php

namespace core;

use \Exception;
use MongoDB\Driver\BulkWrite;
use MongoDB\Driver\Query;
use MongoDB\Driver\Command;
use MongoDB\BSON;

/**
 * @author Jason Wright <jason@silvermast.io>
 * @since 1/4/17
 * @package prunejuice
 */
abstract class Model {
    use Singleton;
    use db\Mongo;

    const ID    = 'id';
    const TABLE = 'default'; // override

    /** @var mixed */
    protected static $_indexes;

    public $dateAdded;
    public $dateModified;

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
        if (is_object($vars)) $vars = get_object_vars($vars);
        foreach ($vars as $key => $val)
            if (property_exists($this, $key)) $this->$key = $val;

        return $this;
    }

    /**
     * @throws Exception
     * @return self
     */
    public abstract function validate();

    /**
     * Saves the object
     * @throws Exception
     * @return self
     */
    public function save() {
        try {
            $this->dateAdded    = $this->dateAdded ?? time();
            $this->dateModified = time();

            // create a new transaction batch
            $bulkWrite = new BulkWrite();
            if (empty($this->{static::ID})) {
                // INSERT
                // generate a new unique ID
                while (empty($this->{static::ID})) {
                    $id_value = static::generateId();
                    // barbaric collision handling
                    if (!self::findOne([static::ID => $this->{static::ID}]))
                        $this->{static::ID} = $id_value;
                }

                $bulkWrite->insert($this);

            } else {
                // UPDATE
                $bulkWrite->update([static::ID => $this->{static::ID}], $this);

            }

            // commit the changes
            self::_db()->executeBulkWrite(static::getDBNamespace(), $bulkWrite);

        } catch (Exception $e) {
            Debug::error($e->getMessage());
            throw new Exception('Unable to save the item. ' . json_encode($this), 500);
        }

        return $this;
    }

    /**
     * Updates only specified fields on an object
     * @param array $set
     * @return static
     * @throws Exception
     */
    public function update($set) {
        try {

            // create a new transaction batch
            $bulkWrite = new BulkWrite();
            if (empty($this->{static::ID}))
                throw new Exception("ID is not set. " . json_encode($this));

            $set                 = (array)$set;
            $set['dateModified'] = time();
            $bulkWrite->update([static::ID => $this->{static::ID}], ['$set' => $set]);

            // commit the changes
            self::_db()->executeBulkWrite(static::getDBNamespace(), $bulkWrite);

        } catch (Exception $e) {
            Debug::error($e->getMessage());
            throw new Exception('Unable to update the item. ' . json_encode($this), 500);
        }

        return $this;
    }

    /**
     * Deletes this object from the database
     * @return self
     */
    public function delete() {
        try {
            $bulkWrite = new BulkWrite();
            $bulkWrite->delete([static::ID => $this->{static::ID}]);
            $db = self::_db();
            $db->executeBulkWrite(static::getDBNamespace(), $bulkWrite);

        } catch (Exception $e) {

        }

        return $this;
    }

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
     * Finds a single object
     * @param $query
     * @param array $queryOptions
     * @return static|null
     */
    public static function findOne($query, $queryOptions = []) {
        try {
            $queryOptions['limit'] = 1;
            $dbQuery               = new Query($query, $queryOptions);
            $cursor                = self::_db()->executeQuery(static::getDBNamespace(), $dbQuery)->toArray();

            return current($cursor) ? static::new(current($cursor)) : null;

        } catch (Exception $e) {
            Debug::error($query);
        }

        return null;
    }

    /**
     * Returns an array of objects (in memory)
     * @param $query
     * @param array $queryOptions
     * @return static[]
     */
    public static function findMulti($query, $queryOptions = []) {
        $objects = [];
        try {
            Debug::info(__METHOD__ . ' ' . json_encode($query));
            $dbQuery = new Query($query, $queryOptions);
            $result  = self::_db()->executeQuery(static::getDBNamespace(), $dbQuery);

            foreach ($result as $row)
                $objects[$row->{static::ID}] = static::new($row);

        } catch (Exception $e) {
            Debug::error($query);
        }

        return $objects;
    }

    /**
     * Returns an array of objects (in memory)
     * @param $query
     * @param array $queryOptions
     * @return mixed
     */
    public static function group($query) {
        $objects = [];
        try {

            if (!isset($query['initial']))
                $query['initial'] = (object)[];

            $cmd    = ['ns' => static::TABLE] + $query;
            $dbCmd  = new Command(['group' => $cmd]);
            $result = self::_db()->executeCommand(static::$dbName, $dbCmd)->toArray();

            return reset($result)->retval;

        } catch (Exception $e) {
            Debug::error(['msg' => $e->getMessage(), 'query' => $query]);
        }

        return $objects;
    }

    /**
     * Returns an array of objects (in memory)
     * @param $query
     * @param array $queryOptions
     * @return mixed
     */
    public static function aggregate($query) {
        $objects = [];
        try {

            $dbCmd = new Command([
                'aggregate' => static::TABLE,
                'pipeline'  => $query,
                //                'cursor' => new \stdClass,
            ]);

            $result = self::_db()->executeCommand(static::$dbName, $dbCmd);

            return $result->toArray();

        } catch (Exception $e) {
            Debug::error(['msg' => $e->getMessage(), 'query' => $query]);
        }

        return $objects;
    }

    /**
     * Returns an array of distinct values
     * @param $field
     * @return array
     */
    public static function distinct($field) {

        $results = [];
        try {

            $dbCmd  = new Command([
                'distinct' => static::TABLE,
                'key'      => $field,
            ]);
            $result = self::_db()->executeCommand(static::$dbName, $dbCmd)->toArray();

            return reset($result)->values;

        } catch (Exception $e) {
            Debug::error($e->getMessage());
        }

        return $results;
    }

    /**
     * Returns the number of objects matching the query
     * @param $query
     * @return int|false
     */
    public static function count($query = []) {
        try {
            $dbCmd  = new Command(['count' => static::TABLE, 'query' => $query]);
            $result = self::_db()->executeCommand(static::$dbName, $dbCmd);

            return $result->toArray()[0]->n;

        } catch (Exception $e) {
            Debug::error($e->getMessage());
        }

        return false;
    }

    /**
     * Deletes objects from the table
     * @param $query
     * @return int|false
     * @throws Exception
     */
    public static function deleteQuery(array $query) {
        if (!count($query))
            throw new Exception('Must provide a valid delete query.');

        try {
            $bulkWrite = new BulkWrite();
            $bulkWrite->delete($query);
            $result = self::_db()->executeBulkWrite(static::getDBNamespace(), $bulkWrite);

            return $result->getDeletedCount();

        } catch (Exception $e) {
            Debug::error($e->getMessage());

            return false;
        }
    }

}