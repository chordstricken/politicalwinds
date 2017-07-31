<?php

namespace core;

use \Exception;

/**
 *
 * @author Jason Wright <jason.dee.wright@gmail.com>
 * @since 7/29/17
 * @package politics
 */
class Git {

    public static function exec($cmd) {
        try {
            if (!$GIT = trim(`which git`))
                throw new Exception('Git is not installed');

            $descriptorspec = [
                0 => ['pipe', 'r'],
                1 => ['pipe', 'w'],
                2 => ['pipe', 'w'],
            ];

            Debug::info("Opening bash proc");
            $ph = proc_open("bash", $descriptorspec, $pipes);
            if (!is_resource($ph))
                throw new Exception("Command failed. $cmd");

            Debug::info("Executing cmd '$GIT $cmd'");
            // execute command
            fwrite($pipes[0], "$GIT $cmd;");
            fclose($pipes[0]);

            $stdout = stream_get_contents($pipes[1]);
            $stderr = stream_get_contents($pipes[2]);

            do {
                $status = proc_get_status($ph);
            } while ($status['running']);

            Debug::info($status);

            fclose($pipes[1]);
            fclose($pipes[2]);

            if (!empty($stderr))
                throw new Exception($stderr);

            // validate the resource
            if ($status['exitcode'] > 0)
                throw new Exception(trim("$stdout\n$stderr"));

            proc_close($ph);

            return $stdout;

        } catch (Exception $e) {

            if (isset($ph) && is_resource($ph)) proc_close($ph);
            throw $e;
        }
    }

    /**
     * Pulls or creates the repository
     * @param $url
     * @param null $path
     * @return string
     */
    public static function pull($url, $path = null) {
        if (!isset($path))
            $path = parse_url($url, PHP_URL_PATH);

        $localDir = self::getLocalDir($path);

        if (!is_dir($localDir)) {
            return self::exec("clone $url $localDir");

        } else {
            return self::exec("-C $localDir pull");
        }
    }

    /**
     * @param $path
     * @return string
     */
    private static function getLocalDir($path) {
        if ($path[0] === '/') $path = substr($path, 1);
        return ROOT . "/repos/$path";
    }
}