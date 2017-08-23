<?php
/**
 *
 * @author Jason Wright <jason.dee.wright@gmail.com>
 * @since 7/20/17
 * @package politics
 *
 */

namespace core\db\traits;
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
    public static function count($query = []) {}
    public static function findOne($query, $queryOptions = []) {}
    public static function findMulti($query, $queryOptions = []) {}
    public static function deleteMulti(array $query) {}
    public static function insertMulti($set, $replace = false) {}
    public static function updateMulti($query, $set) {}

}