<?php
/**
 * Runs a job controller
 */
require(__DIR__ . '/../core.php');

if (!isset($argv[1]))
    die("Usage: $argv[0] <JobName> [<JobParamJson>]");

$class = "\\controllers\\jobs\\$argv[1]";
if (!class_exists($class))
    die("Job $class not found");

$params = null;
if (isset($argv[2])) {
    $params = json_decode($argv[2]) ?? null;
    if (json_last_error()) die(json_last_error_msg());
}

$job = new $class($params);

if (!method_exists($job, 'run'))
    die("Job $argv[1] does not have run method");

$job->run();

die();