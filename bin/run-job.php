<?php
/**
 * @author Jason Wright <jason.dee.wright@gmail.com>
 * @since 7/18/17
 * @package prunejuice
 */
require_once __DIR__ . '/../core.php';

if (!isset($argv[1]))
    die("Usage: $argv[0] <jobType> [<paramsJson>]");

if (!class_exists($controllerName = "controllers\\jobs\\$argv[1]"))
    die("Job Controller not found.");

$params = isset($argv[2]) ? json_decode($argv[2]) : [];

$job = new $controllerName($params);
$job->run();