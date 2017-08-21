<?php

namespace core;

use \Exception;

/**
 * Rudimentary app version controller
 * @author Jason Wright <jason@silvermast.io>
 * @since 1/9/17
 * @package prunejuice
 */
class Update {
    use Singleton;

    const HISTORY_FILE = ROOT . '/data/db-update-history.json';
    const UPDATE_DIR   = ROOT . '/lib/updates';

    /** @var string[] */
    private $history = [];

    /** @var string[] */
    private $scripts = [];

    /**
     * Update constructor.
     */
    public function __construct() {
        $historyDir = dirname(self::HISTORY_FILE);
        if (!is_dir($historyDir) && !mkdir($historyDir, 0777))
            throw new Exception("Failed to create $historyDir");

        if (!is_writable($historyDir))
            throw new Exception("Data directory is not writable.");

        $this->history = json_decode(@file_get_contents(self::HISTORY_FILE)) ?? [];

        // load scripts
        foreach (scandir(self::UPDATE_DIR) as $script)
            if ($script[0] !== '.')
                $this->scripts[] = $script;

        sort($this->scripts);
    }

    /**
     * Executes the database upgrade
     */
    public function run() {
        try {

            $scripts_run = 0;

            foreach ($this->scripts as $script) {
                if (in_array($script, $this->history))
                    continue;

                echo "Running $script\n";
                include(self::UPDATE_DIR . "/$script");
                $scripts_run++;
                $this->history[] = $script; // log the time of completion
            }

            echo $scripts_run ? "Ran $scripts_run scripts.\n" : "No updates were found.\n";

        } catch (\Exception $e) {
            echo $e->getMessage() . "\n";
            echo "Failed completing the update.\n";
        }

        // Ensure that all/any completed scripts are logged
        $this->writeHistory();
    }

    /**
     * Writes the history data
     */
    private function writeHistory() {
        file_put_contents(self::HISTORY_FILE, json_encode($this->history, JSON_PRETTY_PRINT));
    }

}