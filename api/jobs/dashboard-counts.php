<?php
/**
 *
 * @author Jason Wright <jason.dee.wright@gmail.com>
 * @since 7/20/17
 * @package prunejuice
 */
require_once __DIR__ . '/../../core.php';
use models\Job;

$result = Job::group([
    'key' => ['name' => 1, 'status' => 1], // (or '$keyf' if you're passing in a MongoCode object)
    '$reduce' => 'function(curr, result) {
        result.total++;
    }',
    'initial' => ['total' => 0],
]);

core\api\Response::init($result)->send();