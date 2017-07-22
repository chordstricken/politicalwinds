<?php
/**
 *
 * @author Jason Wright <jason.dee.wright@gmail.com>
 * @since 7/20/17
 * @package politics
 *
 */

namespace core\db;
use \Exception;

/**
 * Trait Filesystem
 * @package core\db
 * @property string $path
 */
trait Filesystem {

    /**
     * @return string
     */
    protected function getPath() {
        return '/dev/null';
    }

    /**
     * Creates the directory based off a full path
     * @param $path
     * @throws Exception
     */
    protected function mkdir($path) {
        $dir = dirname($path);
        if (!is_dir($dir) && !mkdir($dir, 0777, true))
            throw new Exception("Failed to create directory $dir");
    }

    /**
     * Saves the object
     */
    public function save() {
        $path = $this->getPath();
        $this->mkdir($path);
        file_put_contents($path, json_encode($this));
    }

    /**
     * @param $set
     */
    public function update($set) {
        $this->setVars($set);
        $this->save();
    }

    /**
     * Deletes the object
     */
    public function delete() {
        unlink($this->path);
    }

    /**
     * @param $query
     * @param array $queryOptions
     * @throws Exception
     */
    public static function findOne($query, $queryOptions = []) {
        if (!isset($query['id'])) throw new Exception('Unable to find document without ID');
    }

    /**
     * @param $query
     * @param array $queryOptions
     */
    public static function findMulti($query, $queryOptions = []) {

    }

    /**
     * @param array $query
     */
    public static function count($query = []) {

    }

    /**
     * @param array $query
     */
    public static function deleteQuery(array $query) {

    }

}