<?php
/**
 * @author Jason Wright <jason.dee.wright@gmail.com>
 * @since 7/18/17
 * @package prunejuice
 */
require_once __DIR__ . '/../core.php';

if (!isset($argv[1]))
    die("Usage: $argv[0] <jobType>");

if (!class_exists($controllerName = "controllers\\schedulers\\$argv[1]"))
    die("Job Controller not found.");

$controllerName::init()->schedule();