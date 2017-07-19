<?php
require_once __DIR__ . '/../../core.php';

$query = isset($_REQUEST['query']) ? $_REQUEST['query'] : [];
$query = is_scalar($query) ? json_decode($query) : $query;

$results = models\Job::findMulti($query, ['sort' => ['startTime' => -1], 'limit' => 50]);

core\api\Response::init($results)->send();