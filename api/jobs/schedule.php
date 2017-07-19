<?php
/**
 * @author Jason Wright <jason.dee.wright@gmail.com>
 * @since 7/17/17
 * @package prunejuice
 */
require_once __DIR__ . '/../../core.php';

use core\api\Response;

if (!isset($_REQUEST['type']))
    Response::init('Please provide a job type', 400)->send();

if (!class_exists($jobClass = "controllers\\jobs\\$_REQUEST[type]"))
    Response::init('Invalid Job type', 400)->send();

$job = new models\Job([
    'name'          => $jobClass,
    'params'        => $_REQUEST['params'] ?? new stdClass,
    'scheduledTime' => intval($_REQUEST['scheduledTime'] || time()),
    'status'        => 'queued',
]);

$job->validate()->save();

Response::init($job, 404);