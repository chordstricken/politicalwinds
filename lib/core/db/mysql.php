<?php

namespace core\db;

use \mysqli;
use \Exception;
use core\Config;

/**
 * MySQL Database connector
 * @author Jason Wright <jason.dee.wright@gmail.com>
 * @since 8/18/17
 * @package politicalwinds
 */
class Mysql extends mysqli {

    /** @var mysqli */
    private static $conn;

    /**
     * @return mysqli
     * @throws Exception
     */
    public static function conn() {
        if (isset(self::$conn))
            return self::$conn;

        $cfg = Config::init();
        if (!isset($cfg->mysql) || !is_object($cfg->mysql))
            throw new Exception("Mysql not configured. Update config.json and try again.");

        self::$conn = new self($cfg->mysql->host, $cfg->mysql->user, $cfg->mysql->pass, $cfg->mysql->database, $cfg->mysql->port);
        self::$conn->set_charset('utf8');

        if ($tz = Config::init()->timezone)
            self::$conn->query('SET time_zone = ' . self::escapeWithQuotes($tz));

        return self::$conn;
    }

    /**
     * @param $str
     * @return string
     */
    public static function escape($str) {
        return self::conn()->real_escape_string($str);
    }

    /**
     * @param $str
     * @return string
     */
    public static function escapeWithQuotes($str) {
        return '"' . self::escape($str) . '"';
    }

    /**
     * Returns a safe field string.
     * "foo.bar.bl ah" => `foo`.`bar`.`bl ah`
     * @param $field
     * @return string
     */
    public static function escapeField($field) {
        return '`' . implode('`.`', explode('.', $field)) . '`';
    }

    /**
     * Builds a query from an array
     * @param $filter
     * @return string
     */
    public static function buildFilter($filter) {
        if (empty($filter))
            return '1';

        if (is_scalar($filter))
            return self::escape($filter);

        $query = [];
        foreach ($filter as $key => $value) {
            $keySafe = self::escapeField($key);

            // IS NULL
            if ($value === null) {
                $query[] = "$keySafe IS NULL";
                continue;
            }

            // allow flat arrays
            if (is_numeric($key) && is_scalar($value)) {
                $query[] = $value;
                continue;
            }

            // basic $key = $value
            if (is_scalar($value)) {
                $valSafe = self::escape($value);
                $query[] = "$keySafe = $valSafe";
                continue;
            }

            // IN array
            if (is_array($value) && count($value)) {
                $valSafe = implode(',', array_map('self::escapeWithQuotes', $value));
                $query[] = "$keySafe IN($valSafe)";
                continue;
            }
        }

        return "\n" . implode(" AND \n", $query) . "\n";
    }

    /**
     * Generates an ORDER BY clause from a sort array.
     * ['foo' => 1, 'bar' => -1] results in "`foo` ASC, `bar` DESC"
     *
     * @param $sort
     * @return string
     */
    public static function buildSort($sort) {
        if (empty($sort))
            return '1'; // ORDER BY 1

        $result = [];
        foreach ($sort as $field => $direction)
            $result[] = self::escapeField($field) . ($direction > 0 ? 'ASC' : 'DESC');

        return implode(', ', $result);
    }

    /**
     * Initiated Methods
     */

    /**
     * @param string $query
     * @param int $resultmode
     * @return bool|\mysqli_result
     * @throws Exception
     */
    public function query($query, $resultmode = MYSQLI_STORE_RESULT) {
        if (!$result = parent::query($query, $resultmode))
            throw new Exception("$this->error\nQuery: $query");
        return $result;
    }

}