<?php
/**
 * Updates the database
 * @author Jason Wright <jason.dee.wright@gmail.com>
 * @since 8/18/17
 * @package politicalwinds
 */
require_once __DIR__ . '/../core.php';
ini_set('display_errors', 1);

try {
    core\Update::init()->run();
    die(0);

} catch (Exception $e) {
    echo $e->getMessage() . PHP_EOL;
    die(1);
}