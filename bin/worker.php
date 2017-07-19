<?php
/**
 * @author Jason Wright <jason.dee.wright@gmail.com>
 * @since 7/18/17
 * @package prunejuice
 */
require_once __DIR__ . '/../core.php';

if (!isset($argv[1]))
    die("Usage: $argv[0] <jobType>");

if (!class_exists($controllerName = "controllers\\jobs\\$argv[1]"))
    die("Job Controller not found.");

/**
 * Loop until end of days
 */
while (true) {

    $query = [
        'name'          => $controllerName,
        'status'        => 'queued',
        'scheduledTime' => ['$lt' => time()],
    ];
    $opts  = ['sort' => ['scheduledTime' => 1]]; // pull oldest scheduled job first

    // No job, sleep for 10 seconds and try again
    if (!$job = models\Job::findOne($query)) {
        sleep(10);
        continue;
    }

    /** @var core\Job $controller */
    $controller = new $controllerName();
    $controller->setJob($job);
    $controller->run();

}