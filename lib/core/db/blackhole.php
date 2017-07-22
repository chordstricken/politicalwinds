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
 * Trait Blackhole
 * @package core\db
 * @property string $path
 */
trait Blackhole {

    public function save() {}
    public function update($set) {}
    public function delete() {}
    public static function findOne($query, $queryOptions = []) {}
    public static function findMulti($query, $queryOptions = []) {}
    public static function count($query = []) {}
    public static function deleteQuery(array $query) {}

}