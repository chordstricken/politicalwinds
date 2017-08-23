<?php
namespace core;
/**
 * Debug class responsible for logging items in /log
 * @author Jason Wright <jason@silvermast.io>
 * @since 2/18/15
 * @package prunejuice
 */

class Debug {

    /**
     * @param [mixed $message [mixed $...]]
     * @return null
     */
    public static function info() {
        $args = func_get_args();
        array_unshift($args, __FUNCTION__);
        return call_user_func_array('self::write', $args);
    }

    /**
     * @param mixed $message
     * @return null
     */
    public static function error($message) {
        $args = func_get_args();
        array_unshift($args, __FUNCTION__);
        return call_user_func_array('self::write', $args);
    }

    /**
     * @param $type
     * @param [string $message, [$...]]
     * @return null
     */
    private static function write() {
        $args    = func_get_args();
        $type    = array_shift($args);
        $message = implode(' ', array_map('self::formatMessage', $args));

        if (!is_dir(ROOT . '/log'))
            @mkdir(ROOT . '/log');

        @error_log(date(DATE_ATOM) . " -- $message\n", 3, ROOT . "/log/$type.log");
        return null;
    }

    /**
     * @param $message
     * @return bool|float|int|string
     */
    private static function formatMessage($message) {
        $message = $message instanceof \Exception ? $message->getMessage() : $message;
        $message = is_scalar($message) ? $message : json_encode($message);
        return $message;
    }
}
